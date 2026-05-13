<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\WijayaPayService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WijayaPayCallbackController extends Controller
{
    public function __construct(
        private readonly WijayaPayService $wijayaPayService,
        private readonly KasirController $kasirController,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $refId = (string) ($request->input('ref_id') ?? data_get($request->input('data'), 'ref_id'));
        $merchantCode = (string) ($request->input('code_merchant') ?? data_get($request->input('data'), 'code_merchant'));
        $signature = (string) $request->header('X-Signature', '');

        $transaction = Transaction::query()
            ->with('outlet')
            ->when($refId, fn ($query) => $query->where('ref_id', $refId))
            ->orWhere('trx_reference', (string) ($request->input('trx_reference') ?? data_get($request->input('data'), 'trx_reference')))
            ->first();

        if (! $transaction) {
            return response('transaction not found', 404);
        }

        $config = $this->wijayaPayService->configForOutlet($transaction->outlet);

        if ($merchantCode && $merchantCode !== $config['merchant_code']) {
            return response('invalid merchant', 422);
        }

        if ($signature && $refId && $signature !== $this->wijayaPayService->signatureFor($refId, $transaction->outlet)) {
            return response('invalid signature', 403);
        }

        $this->kasirController->applyPaymentPayload($transaction, $request->all());

        return response('OK', 200);
    }
}
