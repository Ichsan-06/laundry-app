<?php

namespace App\Http\Controllers;

use App\Models\PlanPurchaseHistory;
use App\Models\SubscriptionPlan;
use App\Services\PlanBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingPaymentController extends Controller
{
    public function __construct(
        private readonly PlanBillingService $planBillingService,
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'purchase_id' => ['nullable', 'exists:plan_purchase_histories,id'],
            'promo_code' => ['nullable', 'string'],
        ]);

        $tenant = $request->user()->tenant;
        $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);

        if ($plan->price_monthly <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Plan ini belum memiliki harga bulanan dan tidak bisa dibeli.',
            ], 422);
        }

        $purchase = DB::transaction(function () use ($validated, $tenant, $plan) {
            $purchase = null;
            $amount = (float) $plan->price_monthly;
            
            if (strtoupper($validated['promo_code'] ?? '') === 'WASHKITAPROMO') {
                $amount *= 0.5;
            }

            if (! empty($validated['purchase_id'])) {
                $purchase = PlanPurchaseHistory::query()
                    ->whereKey($validated['purchase_id'])
                    ->where('tenant_id', $tenant->id)
                    ->where('status', '!=', 'paid')
                    ->first();
            }

            if (! $purchase) {
                $purchase = PlanPurchaseHistory::create([
                    'tenant_id' => $tenant->id,
                    'subscription_plan_id' => $plan->id,
                    'plan_name_snapshot' => $plan->name,
                    'amount' => $amount,
                    'status' => 'pending',
                    'payment_method' => 'QRIS',
                    'ref_id' => 'BILL-' . strtoupper(str_replace('-', '', (string) str()->uuid())),
                ]);
            } else {
                $purchase->update([
                    'subscription_plan_id' => $plan->id,
                    'plan_name_snapshot' => $plan->name,
                    'amount' => $amount,
                    'status' => 'pending',
                    'paid_at' => null,
                ]);
            }

            $payload = $this->planBillingService->createPayment($purchase);
            $normalized = $this->planBillingService->normalizedPayload($payload);

            $purchase->update([
                'trx_reference' => $normalized['trx_reference'] ?: $purchase->trx_reference,
                'payment_fee' => $normalized['total_fee'],
                'payment_name' => $normalized['payment_name'],
                'qr_image' => $normalized['qr_image'],
                'tutorial_pembayaran' => $normalized['tutorial_pembayaran'],
                'payment_expires_at' => $normalized['expired'],
                'status' => $this->planBillingService->toLocalStatus($normalized['status']),
                'last_payload' => $payload,
            ]);

            return $purchase->fresh('plan');
        });

        return response()->json([
            'success' => true,
            'purchase_id' => $purchase->id,
            'plan_id' => $purchase->subscription_plan_id,
            'plan_name' => $purchase->plan_name_snapshot,
            'ref_id' => $purchase->ref_id,
            'trx_reference' => $purchase->trx_reference,
            'payment_status' => $purchase->status,
            'payment_name' => $purchase->payment_name ?: 'QRIS',
            'amount' => (float) $purchase->amount,
            'payment_fee' => (float) $purchase->payment_fee,
            'expired' => optional($purchase->payment_expires_at)->toIso8601String(),
            'qr_image' => $purchase->qr_image,
            'tutorial_pembayaran' => $purchase->tutorial_pembayaran,
        ]);
    }

    public function status(PlanPurchaseHistory $purchase): JsonResponse
    {
        abort_if($purchase->tenant_id !== auth()->user()->tenant_id, 403);

        if ($purchase->status === 'paid') {
            return response()->json([
                'success' => true,
                'purchase_id' => $purchase->id,
                'payment_status' => $purchase->status,
                'trx_reference' => $purchase->trx_reference,
                'paid_at' => optional($purchase->paid_at)->toIso8601String(),
                'expired' => optional($purchase->payment_expires_at)->toIso8601String(),
            ]);
        }

        $payload = $this->planBillingService->checkPaymentStatus($purchase->ref_id);
        $freshPurchase = $this->planBillingService->applyPaymentPayload($purchase, $payload);
        $normalized = $this->planBillingService->normalizedPayload($payload);

        return response()->json([
            'success' => true,
            'purchase_id' => $freshPurchase->id,
            'payment_status' => $freshPurchase->status,
            'third_party_status' => $normalized['status'],
            'payment_name' => $freshPurchase->payment_name,
            'tutorial_pembayaran' => $freshPurchase->tutorial_pembayaran,
            'trx_reference' => $freshPurchase->trx_reference,
            'expired' => optional($freshPurchase->payment_expires_at)->toIso8601String(),
            'paid_at' => optional($freshPurchase->paid_at)->toIso8601String(),
        ]);
    }
}
