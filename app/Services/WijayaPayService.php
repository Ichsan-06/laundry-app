<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WijayaPayService
{
    public function createQrisTransaction(Transaction $transaction): array
    {
        $response = $this->client($transaction->ref_id)->post(config('services.wijayapay.create_url'), [
            'code_merchant' => config('services.wijayapay.merchant_code'),
            'api_key' => config('services.wijayapay.api_key'),
            'code_payment' => 'QRIS',
            'ref_id' => $transaction->ref_id,
            'nominal' => (int) round($transaction->total_amount),
            'amount' => (int) round($transaction->total_amount),
            'callback_url' => config('services.wijayapay.callback_url'),
        ]);

        $payload = $response->json();

        if (! $response->successful() || ! is_array($payload)) {
            throw new RuntimeException('WijayaPay create transaction gagal dihubungi.');
        }

        if ($this->isFailurePayload($payload)) {
            throw new RuntimeException($this->extractMessage($payload, 'WijayaPay gagal membuat transaksi QRIS.'));
        }

        return $payload;
    }

    public function checkTransactionStatus(string $refId): array
    {
        $response = $this->client($refId)->get(config('services.wijayapay.status_url'), [
            'code_merchant' => config('services.wijayapay.merchant_code'),
            'api_key' => config('services.wijayapay.api_key'),
            'ref_id' => $refId,
        ]);

        $payload = $response->json();

        if (! $response->successful() || ! is_array($payload)) {
            throw new RuntimeException('WijayaPay status transaction gagal dihubungi.');
        }

        return $payload;
    }

    public function signatureFor(string $refId): string
    {
        return md5(
            (string) config('services.wijayapay.merchant_code')
            . (string) config('services.wijayapay.api_key')
            . $refId
        );
    }

    public function normalizedTransactionPayload(array $payload): array
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
            'total_bayar' => (float) (Arr::get($data, 'total_bayar') ?? Arr::get($payload, 'total_bayar', 0)),
            'total_fee' => (float) (Arr::get($data, 'total_fee') ?? Arr::get($payload, 'total_fee', 0)),
            'payment_name' => (string) (Arr::get($data, 'payment_name') ?? Arr::get($payload, 'payment_name', 'QRIS')),
            'tutorial_pembayaran' => (string) (Arr::get($data, 'tutorial_pembayaran') ?? Arr::get($payload, 'tutorial_pembayaran', '')),
            'expired' => $this->parseDateTime(
                Arr::get($data, 'expired')
                ?? Arr::get($payload, 'expired')
                ?? Arr::get($data, 'expired_at')
                ?? Arr::get($payload, 'expired_at')
            ),
            'message' => $this->extractMessage($payload),
        ];
    }

    public function toLocalPaymentStatus(string $remoteStatus): string
    {
        return match (strtolower($remoteStatus)) {
            'paid', 'success', 'settlement', 'completed' => 'paid',
            'expired', 'cancel', 'cancelled', 'failed' => 'expired',
            default => 'pending',
        };
    }

    private function client(string $refId): PendingRequest
    {
        return Http::acceptJson()
            ->asForm()
            ->timeout(30)
            ->withHeaders([
                'X-Signature' => $this->signatureFor($refId),
            ]);
    }

    private function extractMessage(array $payload, string $fallback = 'Terjadi kesalahan pada response WijayaPay.'): string
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
