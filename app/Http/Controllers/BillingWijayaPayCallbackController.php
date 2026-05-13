<?php

namespace App\Http\Controllers;

use App\Models\PlanPurchaseHistory;
use App\Services\PlanBillingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BillingWijayaPayCallbackController extends Controller
{
    public function __construct(
        private readonly PlanBillingService $planBillingService,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $refId = (string) ($request->input('ref_id') ?? data_get($request->input('data'), 'ref_id'));
        $merchantCode = (string) ($request->input('code_merchant') ?? data_get($request->input('data'), 'code_merchant'));
        $signature = (string) $request->header('X-Signature', '');

        $purchase = PlanPurchaseHistory::query()
            ->where('ref_id', $refId)
            ->orWhere('trx_reference', (string) ($request->input('trx_reference') ?? data_get($request->input('data'), 'trx_reference')))
            ->first();

        if (! $purchase) {
            return response('purchase not found', 404);
        }

        $config = $this->planBillingService->config();

        if ($merchantCode && $merchantCode !== $config['merchant_code']) {
            return response('invalid merchant', 422);
        }

        if ($signature && $refId && $signature !== $this->planBillingService->signatureFor($refId)) {
            return response('invalid signature', 403);
        }

        $this->planBillingService->applyPaymentPayload($purchase, $request->all());

        return response('OK', 200);
    }
}
