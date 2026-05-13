<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\AddonOption;
use App\Models\Machine;
use App\Models\SelfServiceDetail;
use App\Models\ServicePackage;
use App\Services\TenantContextService;
use App\Services\WijayaPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    public function __construct(
        private readonly WijayaPayService $wijayaPayService,
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $activeOutlet = $user->isOwner()
            ? Outlet::query()->where('tenant_id', $user->tenant_id)->orderBy('nama_outlet')->first()
            : $user->outlet;
        $members = $this->tenantContextService->scopeByUser(Member::query(), $user)->orderBy('nama', 'asc')->get();
        $machines = $this->tenantContextService->scopeByUser(Machine::with('durations'), $user)->get();
        $services = $this->tenantContextService->scopeByUser(ServicePackage::query(), $user)->where('aktif', true)->get();
        $addons = $this->tenantContextService->scopeByUser(AddonOption::query(), $user)->where('aktif', true)->get();

        return view('pages.kasir', [
            'members' => $members,
            'machines' => $machines,
            'services' => $services,
            'addons' => $addons,
            'qrisConfigReady' => $this->hasCompleteWijayaPayConfig($activeOutlet),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCheckoutRequest($request, false);

        try {
            $transaction = DB::transaction(function () use ($validated, $request) {
                $prepared = $this->prepareTransactionData($validated, $request);
                $transaction = $this->createTransactionRecord($validated, $prepared, [
                    'status' => 'IN_PROGRESS',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);

                $this->persistTransactionDetails($transaction, $validated, $prepared, true);

                return $transaction;
            });

            return redirect()->route('kasir.index')->with([
                'success' => 'Transaksi berhasil diproses.',
                'last_transaction' => [
                    'id' => $transaction->id,
                    'number' => $transaction->transaction_number,
                    'total' => $transaction->total_amount,
                    'received' => $transaction->amount_received,
                    'change' => $transaction->change_amount,
                ],
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function createQrisPayment(Request $request): JsonResponse
    {
        $validated = $this->validateCheckoutRequest($request, true);
        $user = $request->user();
        $activeOutlet = $user->isOwner()
            ? Outlet::query()->where('tenant_id', $user->tenant_id)->orderBy('nama_outlet')->first()
            : $user->outlet;

        if (! $this->hasCompleteWijayaPayConfig($activeOutlet)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan WijayaPay untuk outlet ini belum lengkap. Silakan lengkapi dulu di menu Settings.',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $request) {
                $prepared = $this->prepareTransactionData($validated, $request, true);
                $existingTransaction = null;

                if (! empty($validated['transaction_id'])) {
                    $existingTransaction = Transaction::whereKey($validated['transaction_id'])
                        ->where('payment_method', 'QRIS')
                        ->first();
                }

                if ($existingTransaction) {
                    $this->syncTransactionRecord($existingTransaction, $validated, $prepared, [
                        'status' => 'PENDING',
                        'payment_status' => 'pending',
                        'paid_at' => null,
                    ]);
                    $transaction = $existingTransaction->fresh();
                } else {
                    $transaction = $this->createTransactionRecord($validated, $prepared, [
                        'status' => 'PENDING',
                        'payment_status' => 'pending',
                        'amount_received' => 0,
                        'change_amount' => 0,
                    ]);
                    $this->persistTransactionDetails($transaction, $validated, $prepared, false);
                }

                if (! $transaction->ref_id) {
                    $transaction->update([
                        'ref_id' => 'WP-' . strtoupper(str_replace('-', '', $transaction->id)),
                    ]);
                }

                $payload = $this->wijayaPayService->createQrisTransaction($transaction);

                $normalized = $this->wijayaPayService->normalizedTransactionPayload($payload);

                $transaction->update([
                    'trx_reference' => $normalized['trx_reference'] ?: $transaction->trx_reference,
                    'payment_fee' => $normalized['total_fee'],
                    'payment_expires_at' => $normalized['expired'],
                    'payment_status' => $this->wijayaPayService->toLocalPaymentStatus($normalized['status']),
                ]);

                return [
                    'transaction' => $transaction->fresh(),
                    'qris' => $normalized,
                ];
            });

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction['transaction']->id,
                'transaction_number' => $transaction['transaction']->transaction_number,
                'ref_id' => $transaction['transaction']->ref_id,
                'trx_reference' => $transaction['transaction']->trx_reference,
                'payment_status' => $transaction['transaction']->payment_status,
                'payment_name' => $transaction['qris']['payment_name'],
                'total_bayar' => (float) $transaction['transaction']->total_amount,
                'total_fee' => (float) $transaction['transaction']->payment_fee,
                'expired' => optional($transaction['transaction']->payment_expires_at)->toIso8601String(),
                'qr_image' => $transaction['qris']['qr_image'],
                'tutorial_pembayaran' => $transaction['qris']['tutorial_pembayaran'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkQrisStatus(Transaction $transaction): JsonResponse
    {
        abort_if(! in_array($transaction->outlet_id, auth()->user()->accessibleOutletIds(), true), 403);

        if ($transaction->payment_method !== 'QRIS' || ! $transaction->ref_id) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi QRIS tidak valid.',
            ], 422);
        }

        try {
            $payload = $this->wijayaPayService->checkTransactionStatus($transaction->ref_id);
            $normalized = $this->wijayaPayService->normalizedTransactionPayload($payload);
            $this->applyPaymentPayload($transaction, $payload);
            $freshTransaction = $transaction->fresh();

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'payment_status' => $freshTransaction->payment_status,
                'status' => $freshTransaction->payment_status,
                'third_party_status' => $normalized['status'],
                'payment_name' => $normalized['payment_name'],
                'tutorial_pembayaran' => $normalized['tutorial_pembayaran'],
                'trx_reference' => $freshTransaction->trx_reference,
                'expired' => optional($freshTransaction->payment_expires_at)->toIso8601String(),
                'paid_at' => optional($freshTransaction->paid_at)->toIso8601String(),
                'message' => 'Status pembayaran berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function printReceipt($id)
    {
        $transaction = Transaction::with([
            'member',
            'cashier',
            'selfServiceDetails.machine',
            'servicePackages',
            'addonOptions',
            'items',
        ])->findOrFail($id);
        abort_if(! in_array($transaction->outlet_id, auth()->user()->accessibleOutletIds(), true), 403);

        return view('pages.receipt', compact('transaction'));
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['outlet_id'] = $request->user()->isOwner()
            ? Outlet::query()->where('tenant_id', $request->user()->tenant_id)->orderBy('nama_outlet')->value('id')
            : $request->user()->outlet_id;
        $validated['id_member'] = 'MBR-' . strtoupper(bin2hex(random_bytes(2)));
        $validated['saldo'] = 0;
        $validated['status'] = 'AKTIF';
        $validated['tanggal_daftar'] = now();

        $member = Member::create($validated);

        return response()->json([
            'success' => true,
            'member' => $member,
        ]);
    }

    public function applyPaymentPayload(Transaction $transaction, array $payload): void
    {
        $normalized = $this->wijayaPayService->normalizedTransactionPayload($payload);
        $localStatus = $this->wijayaPayService->toLocalPaymentStatus($normalized['status']);

        $transaction->update([
            'payment_status' => $localStatus,
            'trx_reference' => $normalized['trx_reference'] ?: $transaction->trx_reference,
            'payment_fee' => $normalized['total_fee'] ?: $transaction->payment_fee,
            'payment_expires_at' => $normalized['expired'] ?: $transaction->payment_expires_at,
            'paid_at' => $localStatus === 'paid' ? ($transaction->paid_at ?? now()) : $transaction->paid_at,
            'status' => $localStatus === 'paid' ? 'IN_PROGRESS' : ($localStatus === 'expired' ? 'CANCELLED' : $transaction->status),
            'amount_received' => $localStatus === 'paid' ? ($transaction->total_amount + $transaction->payment_fee) : $transaction->amount_received,
            'change_amount' => 0,
        ]);

        if ($localStatus === 'paid') {
            $this->activatePaidTransaction($transaction->fresh());
        }
    }

    private function hasCompleteWijayaPayConfig(?Outlet $outlet): bool
    {
        if (! $outlet) {
            return false;
        }

        return filled($outlet->wijayapay_merchant_code)
            && filled($outlet->wijayapay_api_key)
            && filled($outlet->wijayapay_create_url)
            && filled($outlet->wijayapay_status_url);
    }

    private function validateCheckoutRequest(Request $request, bool $forQris): array
    {
        $rules = [
            'service_type' => 'required|in:SELF_SERVICE,DROP_OFF',
            'member_id' => 'nullable|exists:members,id',
            'payment_method' => 'required|string',
            'amount_received' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'machine_ids' => 'required_if:service_type,SELF_SERVICE|string|nullable',
            'drop_off_details' => 'required_if:service_type,DROP_OFF|string|nullable',
            'addon_ids' => 'nullable|string',
            'items' => 'required_if:service_type,DROP_OFF|string|nullable',
            'note' => 'nullable|string',
            'estimated_finish' => 'nullable|string',
        ];

        if ($forQris) {
            $rules['transaction_id'] = 'nullable|exists:transactions,id';
        }

        $validated = $request->validate($rules);

        if ($forQris) {
            $validated['payment_method'] = 'QRIS';
            $validated['amount_received'] = 0;
        }

        return $validated;
    }

    private function prepareTransactionData(array $validated, Request $request, bool $forQris = false): array
    {
        $member = ! empty($validated['member_id']) ? Member::find($validated['member_id']) : null;
        $cashier = $request->user() ?? User::first();
        $outletId = $cashier?->isOwner()
            ? Outlet::query()->where('tenant_id', $cashier->tenant_id)->orderBy('nama_outlet')->value('id')
            : $cashier?->outlet_id;
        $subtotal = 0.0;
        $totalWeight = 0.0;
        $transactionDetails = [];
        $dropOffDetails = [];
        $items = [];

        if ($member && ! in_array($member->outlet_id, $cashier->accessibleOutletIds(), true)) {
            throw ValidationException::withMessages([
                'member_id' => 'Member tidak termasuk outlet yang dapat Anda akses.',
            ]);
        }

        if ($validated['service_type'] === 'SELF_SERVICE') {
            $machineIds = array_values(array_filter(explode(',', $validated['machine_ids'] ?? '')));

            if (empty($machineIds)) {
                throw ValidationException::withMessages([
                    'checkout' => 'Keranjang kosong. Pilih minimal satu mesin sebelum checkout.',
                ]);
            }

            $machines = Machine::with('durations')
                ->whereIn('id', $machineIds)
                ->whereIn('outlet_id', $cashier->accessibleOutletIds())
                ->get();

            if ($machines->count() !== count($machineIds)) {
                throw ValidationException::withMessages([
                    'checkout' => 'Sebagian mesin tidak ditemukan. Silakan pilih ulang mesin.',
                ]);
            }

            foreach ($machines as $machine) {
                if ($machine->status !== 'AVAILABLE') {
                    throw ValidationException::withMessages([
                        'checkout' => "Stok tidak mencukupi. Mesin {$machine->machine_code} sudah tidak tersedia.",
                    ]);
                }

                $duration = $machine->durations
                    ->firstWhere('duration_type', $machine->machine_type === 'WASHER' ? 'WASH' : 'DRY')
                    ?? $machine->durations->first();

                if (! $duration) {
                    throw ValidationException::withMessages([
                        'checkout' => "Durasi mesin {$machine->machine_code} tidak ditemukan.",
                    ]);
                }

                $subtotal += (float) $duration->price;
                $transactionDetails[] = [
                    'machine' => $machine,
                    'duration' => $duration,
                ];
            }
        } else {
            $dropOffDetails = json_decode($validated['drop_off_details'] ?? '[]', true);
            $items = json_decode($validated['items'] ?? '[]', true);

            if (! is_array($dropOffDetails) || count($dropOffDetails) === 0) {
                throw ValidationException::withMessages([
                    'checkout' => 'Keranjang kosong. Tambahkan detail layanan terlebih dahulu.',
                ]);
            }

            if (! is_array($items) || count($items) === 0) {
                throw ValidationException::withMessages([
                    'checkout' => 'Keranjang kosong. Tambahkan minimal satu item cucian.',
                ]);
            }

            $validItems = collect($items)->filter(fn ($item) => ! empty($item['nama']))->values();

            if ($validItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'checkout' => 'Keranjang kosong. Isi minimal satu nama item cucian.',
                ]);
            }

            foreach ($dropOffDetails as $detail) {
                if (! empty($detail['package_id'])) {
                    $pkg = ServicePackage::query()
                        ->whereKey($detail['package_id'])
                        ->whereIn('outlet_id', $cashier->accessibleOutletIds())
                        ->first();

                    if ($pkg) {
                        $weight = (float) ($detail['weight'] ?? 1);

                        if ($weight <= 0) {
                            throw ValidationException::withMessages([
                                'checkout' => 'Berat cucian harus lebih dari 0.',
                            ]);
                        }

                        $totalWeight += $weight;
                        $actualWeight = max($weight, (float) $pkg->berat_minimal);
                        $subtotal += $actualWeight * (float) $pkg->harga_per_kg;
                    }
                }
            }

            foreach ($validItems as $item) {
                $qty = (int) ($item['qty'] ?? 0);

                if ($qty <= 0) {
                    throw ValidationException::withMessages([
                        'checkout' => "Jumlah item untuk {$item['nama']} harus lebih dari 0.",
                    ]);
                }

                if (isset($item['available_stock']) && $item['available_stock'] !== null && $qty > (int) $item['available_stock']) {
                    throw ValidationException::withMessages([
                        'checkout' => "Stok tidak mencukupi untuk {$item['nama']}. Sisa stok {$item['available_stock']}.",
                    ]);
                }
            }

            $addonIds = array_filter(explode(',', $validated['addon_ids'] ?? ''));
            if (! empty($addonIds)) {
                $addons = AddonOption::query()
                    ->whereIn('id', $addonIds)
                    ->whereIn('outlet_id', $cashier->accessibleOutletIds())
                    ->get();
                foreach ($addons as $addon) {
                    $subtotal += (float) $addon->harga;
                }
            }
        }

        $totalAmount = (float) $validated['total_amount'];
        $amountReceived = (float) $validated['amount_received'];
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $taxAmount = (float) ($validated['tax_amount'] ?? 0);

        if (! $forQris && $validated['payment_method'] === 'CASH') {
            if ($amountReceived <= 0) {
                throw ValidationException::withMessages([
                    'amount_received' => 'Nominal uang wajib diisi untuk pembayaran tunai.',
                ]);
            }

            if ($amountReceived < $totalAmount) {
                throw ValidationException::withMessages([
                    'amount_received' => 'Uang yang dibayar kurang Rp ' . number_format($totalAmount - $amountReceived, 0, ',', '.'),
                ]);
            }
        }

        if (round($subtotal - $discountAmount + $taxAmount, 2) !== round($totalAmount, 2)) {
            throw ValidationException::withMessages([
                'checkout' => 'Total pembayaran tidak valid. Silakan cek kembali keranjang transaksi.',
            ]);
        }

        return [
            'member' => $member,
            'cashier' => $cashier,
            'outlet_id' => $outletId,
            'subtotal' => $subtotal,
            'total_weight' => $totalWeight,
            'transaction_details' => $transactionDetails,
            'drop_off_details' => $dropOffDetails,
            'items' => $items,
            'total_amount' => $totalAmount,
            'amount_received' => $amountReceived,
        ];
    }

    private function createTransactionRecord(array $validated, array $prepared, array $overrides = []): Transaction
    {
        $attributes = array_merge([
            'outlet_id' => $prepared['outlet_id'],
            'cashier_id' => $prepared['cashier']->id,
            'member_id' => $prepared['member']?->id,
            'transaction_number' => 'TRX-' . strtoupper(bin2hex(random_bytes(3))),
            'transaction_type' => $validated['service_type'],
            'service_type' => 'COMPLETE',
            'weight' => $validated['service_type'] === 'DROP_OFF' ? $prepared['total_weight'] : null,
            'estimated_finish' => $validated['service_type'] === 'DROP_OFF' && ! empty($validated['estimated_finish']) ? $validated['estimated_finish'] : null,
            'status' => 'PENDING',
            'subtotal' => $prepared['subtotal'],
            'member_discount' => 0,
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'tax_percent' => $validated['tax_percent'] ?? 0,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'total_amount' => $prepared['total_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'trx_reference' => null,
            'ref_id' => 'WP-' . strtoupper(Str::ulid()),
            'payment_fee' => 0,
            'payment_expires_at' => null,
            'paid_at' => null,
            'amount_received' => $prepared['amount_received'],
            'change_amount' => max(0, $prepared['amount_received'] - $prepared['total_amount']),
            'notes' => $validated['note'] ?? null,
        ], $overrides);

        return Transaction::create($attributes);
    }

    private function syncTransactionRecord(Transaction $transaction, array $validated, array $prepared, array $overrides = []): void
    {
        $transaction->update(array_merge([
            'member_id' => $prepared['member']?->id,
            'weight' => $validated['service_type'] === 'DROP_OFF' ? $prepared['total_weight'] : null,
            'estimated_finish' => $validated['service_type'] === 'DROP_OFF' && ! empty($validated['estimated_finish']) ? $validated['estimated_finish'] : null,
            'subtotal' => $prepared['subtotal'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'tax_percent' => $validated['tax_percent'] ?? 0,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'total_amount' => $prepared['total_amount'],
            'notes' => $validated['note'] ?? null,
            'payment_fee' => 0,
            'payment_expires_at' => null,
        ], $overrides));
    }

    private function persistTransactionDetails(Transaction $transaction, array $validated, array $prepared, bool $activateMachines): void
    {
        if ($validated['service_type'] === 'SELF_SERVICE') {
            foreach ($prepared['transaction_details'] as $detail) {
                SelfServiceDetail::create([
                    'transaction_id' => $transaction->id,
                    'machine_id' => $detail['machine']->id,
                    'machine_duration_id' => $detail['duration']->id,
                    'duration_minutes' => $detail['duration']->duration_minutes,
                    'price' => $detail['duration']->price,
                    'start_time' => $activateMachines ? now() : null,
                    'end_time' => $activateMachines ? now()->addMinutes($detail['duration']->duration_minutes) : null,
                    'machine_status' => $activateMachines ? 'RUNNING' : 'STOPPED',
                ]);

                if ($activateMachines) {
                    $detail['machine']->update(['status' => 'IN_USE']);
                }
            }

            return;
        }

        foreach ($prepared['drop_off_details'] as $detail) {
            if (! empty($detail['package_id'])) {
                $pkg = ServicePackage::find($detail['package_id']);
                if ($pkg) {
                    $transaction->servicePackages()->attach($pkg->id, [
                        'id' => Str::uuid(),
                        'weight' => $detail['weight'] ?? 1,
                        'note' => $detail['note'] ?? null,
                        'price' => $pkg->harga_per_kg,
                    ]);
                }
            }
        }

        $addonIds = array_filter(explode(',', $validated['addon_ids'] ?? ''));
        if (! empty($addonIds)) {
            $addons = AddonOption::whereIn('id', $addonIds)->get();
            foreach ($addons as $addon) {
                $transaction->addonOptions()->attach($addon->id, [
                    'id' => Str::uuid(),
                    'price' => $addon->harga,
                ]);
            }
        }

        foreach ($prepared['items'] as $item) {
            if (! empty($item['nama'])) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'nama_item' => $item['nama'],
                    'qty' => $item['qty'] ?? 1,
                    'note' => $item['note'] ?? null,
                ]);
            }
        }
    }

    private function activatePaidTransaction(Transaction $transaction): void
    {
        if ($transaction->payment_status !== 'paid') {
            return;
        }

        $transaction->loadMissing('selfServiceDetails.machine', 'selfServiceDetails.machineDuration');

        foreach ($transaction->selfServiceDetails as $detail) {
            if (! $detail->start_time) {
                $detail->update([
                    'start_time' => now(),
                    'end_time' => now()->addMinutes($detail->duration_minutes),
                    'machine_status' => 'RUNNING',
                ]);
            }

            if ($detail->machine && $detail->machine->status === 'AVAILABLE') {
                $detail->machine->update(['status' => 'IN_USE']);
            }
        }
    }
}
