<?php

namespace App\Services;

use App\Models\PlanPurchaseHistory;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PlanBillingService
{
    public function createPayment(PlanPurchaseHistory $purchase): array
    {
        $config = $this->config();

        $response = $this->client($purchase->ref_id)->post($config['create_url'], [
            'code_merchant' => $config['merchant_code'],
            'api_key' => $config['api_key'],
            'code_payment' => 'QRIS',
            'ref_id' => $purchase->ref_id,
            'nominal' => (int) $purchase->amount,
            'amount' => (int) $purchase->amount,
            'callback_url' => route('callback.wijayapay.billing'),
        ]);

        $payload = $response->json();

        if (! $response->successful() || ! is_array($payload)) {
            throw new RuntimeException('WijayaPay create payment billing gagal dihubungi.');
        }

        if ($this->isFailurePayload($payload)) {
            throw new RuntimeException($this->extractMessage($payload, 'Gagal membuat pembayaran upgrade plan.'));
        }

        return $payload;
    }

    public function checkPaymentStatus(string $refId): array
    {
        $config = $this->config();

        $response = $this->client($refId)->get($config['status_url'], [
            'code_merchant' => $config['merchant_code'],
            'api_key' => $config['api_key'],
            'ref_id' => $refId,
        ]);

        $payload = $response->json();

        if (! $response->successful() || ! is_array($payload)) {
            throw new RuntimeException('WijayaPay status payment billing gagal dihubungi.');
        }

        return $payload;
    }

    public function normalizedPayload(array $payload): array
    {
        $data = Arr::get($payload, 'data', []);

        return [
            'raw' => $payload,
            'status' => strtolower((string) (
                Arr::get($data, 'status')
                ?? Arr::get($payload, 'status_pembayaran')
                ?? Arr::get($payload, 'status')
                ?? 'pending'
            )),
            'trx_reference' => (string) (Arr::get($data, 'trx_reference') ?? Arr::get($payload, 'trx_reference', '')),
            'qr_image' => Arr::get($data, 'qr_image') ?? Arr::get($payload, 'qr_image'),
            'total_fee' => (float) (Arr::get($data, 'total_fee') ?? Arr::get($payload, 'total_fee', 0)),
            'payment_name' => (string) (Arr::get($data, 'payment_name') ?? Arr::get($payload, 'payment_name', 'QRIS')),
            'tutorial_pembayaran' => (string) (Arr::get($data, 'tutorial_pembayaran') ?? Arr::get($payload, 'tutorial_pembayaran', '')),
            'expired' => $this->parseDateTime(
                Arr::get($data, 'expired')
                ?? Arr::get($payload, 'expired')
                ?? Arr::get($data, 'expired_at')
                ?? Arr::get($payload, 'expired_at')
            ),
        ];
    }

    public function toLocalStatus(string $remoteStatus): string
    {
        return match (strtolower($remoteStatus)) {
            'paid', 'success', 'settlement', 'completed' => 'paid',
            'expired', 'cancel', 'cancelled', 'failed' => 'expired',
            default => 'pending',
        };
    }

    public function applyPaymentPayload(PlanPurchaseHistory $purchase, array $payload): PlanPurchaseHistory
    {
        $normalized = $this->normalizedPayload($payload);
        $localStatus = $this->toLocalStatus($normalized['status']);

        DB::transaction(function () use ($purchase, $payload, $normalized, $localStatus) {
            $purchase->update([
                'status' => $localStatus,
                'trx_reference' => $normalized['trx_reference'] ?: $purchase->trx_reference,
                'payment_fee' => $normalized['total_fee'] ?: $purchase->payment_fee,
                'payment_name' => $normalized['payment_name'] ?: $purchase->payment_name,
                'qr_image' => $normalized['qr_image'] ?: $purchase->qr_image,
                'tutorial_pembayaran' => $normalized['tutorial_pembayaran'] ?: $purchase->tutorial_pembayaran,
                'payment_expires_at' => $normalized['expired'] ?: $purchase->payment_expires_at,
                'paid_at' => $localStatus === 'paid' ? ($purchase->paid_at ?? now()) : $purchase->paid_at,
                'last_payload' => $payload,
            ]);

            if ($localStatus === 'paid' && ! $purchase->activated_subscription_id) {
                $subscription = $this->activatePlan($purchase->tenant, $purchase->plan);
                $purchase->update(['activated_subscription_id' => $subscription->id]);
            }
        });

        return $purchase->fresh(['plan', 'activatedSubscription']);
    }

    public function activatePlan(Tenant $tenant, SubscriptionPlan $plan): TenantSubscription
    {
        $currentSubscription = $tenant->subscriptions()->latest('created_at')->first();

        if ($currentSubscription && $currentSubscription->status !== 'expired') {
            $currentSubscription->update([
                'status' => 'expired',
                'expired_at' => now(),
            ]);
        }

        return TenantSubscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'trial_ends_at' => null,
            'expired_at' => null,
            'is_trial' => false,
            'grace_dashboard_only' => false,
        ]);
    }

    public function signatureFor(string $refId): string
    {
        $config = $this->config();

        return md5($config['merchant_code'] . $config['api_key'] . $refId);
    }

    public function config(): array
    {
        return [
            'merchant_code' => (string) config('services.wijayapay.merchant_code'),
            'api_key' => (string) config('services.wijayapay.api_key'),
            'create_url' => (string) config('services.wijayapay.create_url'),
            'status_url' => (string) config('services.wijayapay.status_url'),
        ];
    }

    private function client(string $refId): PendingRequest
    {
        $config = $this->config();

        return Http::acceptJson()
            ->asForm()
            ->timeout(30)
            ->withHeaders([
                'X-Signature' => md5($config['merchant_code'] . $config['api_key'] . $refId),
            ]);
    }

    private function extractMessage(array $payload, string $fallback): string
    {
        return (string) (
            Arr::get($payload, 'message')
            ?? Arr::get($payload, 'msg')
            ?? Arr::get($payload, 'data.message')
            ?? $fallback
        );
    }

    private function isFailurePayload(array $payload): bool
    {
        $status = strtolower((string) ($payload['status'] ?? ''));
        $success = $payload['success'] ?? null;
        $code = (int) ($payload['code'] ?? 200);

        return $success === false
            || in_array($status, ['error', 'failed', 'fail'], true)
            || $code >= 400;
    }

    private function parseDateTime(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
