<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\ServicePackage;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TenantContextService;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['member', 'cashier', 'outlet']);
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function($mq) use ($search) {
                      $mq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by Transaction Type (Tab)
        if ($request->has('transaction_type') && $request->transaction_type !== 'all') {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by Service Type / Package
        if ($request->has('service_type') && $request->service_type !== 'all') {
            $serviceType = $request->service_type;
            if (\Illuminate\Support\Str::isUuid($serviceType)) {
                $query->whereHas('servicePackages', function($q) use ($serviceType) {
                    $q->where('service_packages.id', $serviceType);
                });
            } else {
                $query->where('service_type', $serviceType);
            }
        }

        // Filter by Payment Method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Outlet (For Owner/Admin)
        if ($request->has('outlet_id') && $request->outlet_id !== 'all') {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->filled('date_range')) {
            $today = Carbon::today();

            match ($request->date_range) {
                'today' => $query->whereDate('created_at', $today),
                'yesterday' => $query->whereDate('created_at', $today->copy()->subDay()),
                'last_7_days' => $query->whereDate('created_at', '>=', $today->copy()->subDays(6)),
                'last_30_days' => $query->whereDate('created_at', '>=', $today->copy()->subDays(29)),
                'this_month' => $query->whereBetween('created_at', [
                    $today->copy()->startOfMonth()->startOfDay(),
                    $today->copy()->endOfMonth()->endOfDay(),
                ]),
                'custom' => $this->applyCustomDateRange($query, $request),
                default => null,
            };
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'highest_amount') {
            $query->orderBy('total_amount', 'desc');
        } elseif ($sort === 'lowest_amount') {
            $query->orderBy('total_amount', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(10)->withQueryString();
        $statsQuery = Transaction::query();
        $statsQuery = $this->tenantContextService->scopeByUser($statsQuery, $request->user());

        // Statistics
        $stats = [
            'total_revenue' => (clone $statsQuery)->where('status', 'COMPLETED')->sum('total_amount'),
            'total_orders' => (clone $statsQuery)->count(),
            'active_orders' => (clone $statsQuery)->whereIn('status', ['PENDING', 'IN_PROGRESS'])->count(),
            'completed_today' => (clone $statsQuery)->where('status', 'COMPLETED')
                                            ->whereDate('created_at', now()->toDateString())
                                            ->count(),
        ];

        // For modals
        $members = $this->tenantContextService->scopeByUser(Member::query(), $request->user())->get();
        $outlets = $request->user()->isSuperAdmin()
            ? Outlet::all()
            : Outlet::query()->whereIn('id', $request->user()->accessibleOutletIds())->get();
        $servicePackages = $this->tenantContextService->scopeByUser(ServicePackage::query(), $request->user())->get();
        $cashiers = User::query()
            ->when(! $request->user()->isSuperAdmin(), fn ($builder) => $builder->where('tenant_id', $request->user()->tenant_id))
            ->get();

        return view('pages.transactions.index', compact('transactions', 'stats', 'members', 'outlets', 'cashiers', 'servicePackages'));
    }

    private function applyCustomDateRange($query, Request $request): void
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date)->toDateString());
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date)->toDateString());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'cashier_id' => 'required|exists:users,id',
            'member_id' => 'nullable|exists:members,id',
            'service_type' => 'required|in:WASH_ONLY,DRY_ONLY,WASH_DRY,IRONING,COMPLETE',
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'subtotal' => 'required|numeric|min:0',
            'member_discount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:CASH,TRANSFER,E_WALLET,QRIS',
            'amount_received' => 'required|numeric|min:0',
            'change_amount' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['transaction_number'] = 'TRX-' . strtoupper(bin2hex(random_bytes(3)));
        
        // Auto calculation
        $validated['total_amount'] = $validated['subtotal'] - ($validated['member_discount'] ?? 0);
        $validated['change_amount'] = $validated['amount_received'] - $validated['total_amount'];

        Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN_PROGRESS,READY,COMPLETED,CANCELLED',
            'payment_method' => 'required|in:CASH,TRANSFER,E_WALLET,QRIS',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $oldStatus = $transaction->status;

            if ($transaction->isDropOff()) {
                $validated['process_step'] = match ($validated['status']) {
                    'READY' => 'READY',
                    'COMPLETED' => 'PICKED_UP',
                    'IN_PROGRESS', 'PENDING' => $transaction->process_step ?: 'RECEIVED',
                    default => $transaction->process_step,
                };
            }

            $transaction->update($validated);

            // Handle machine availability for self service
            if ($transaction->transaction_type === 'SELF_SERVICE') {
                if (in_array($validated['status'], ['COMPLETED', 'CANCELLED'])) {
                    foreach ($transaction->selfServiceDetails as $detail) {
                        if ($detail->machine) {
                            $detail->machine->update(['status' => 'AVAILABLE']);
                            $detail->update(['machine_status' => 'STOPPED']);
                        }
                    }
                } elseif ($validated['status'] === 'IN_PROGRESS' && $oldStatus !== 'IN_PROGRESS') {
                    // If moving back to in progress, mark machines as in use
                    foreach ($transaction->selfServiceDetails as $detail) {
                        if ($detail->machine && $detail->machine->status === 'AVAILABLE') {
                            $detail->machine->update(['status' => 'IN_USE']);
                            $detail->update(['machine_status' => 'RUNNING']);
                        }
                    }
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function advanceProcess(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        abort_unless($transaction->isDropOff(), 404);

        $validated = $request->validate([
            'step' => 'required|in:' . implode(',', Transaction::DROP_OFF_PROCESS_STEPS),
            'notify' => 'nullable|boolean',
        ]);

        $nextStep = $transaction->nextProcessStep();

        if ($validated['step'] !== $nextStep) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->with('error', 'Urutan status laundry tidak valid.');
        }

        [$status, $successMessage] = match ($validated['step']) {
            'READY' => ['READY', 'Transaksi ditandai selesai dan siap diambil.'],
            'PICKED_UP' => ['COMPLETED', 'Transaksi ditandai sudah diambil pelanggan.'],
            default => ['IN_PROGRESS', 'Status proses laundry berhasil diperbarui.'],
        };

        $transaction->update([
            'process_step' => $validated['step'],
            'status' => $status,
        ]);

        if ((bool) ($validated['notify'] ?? false) && $validated['step'] === 'READY') {
            $waUrl = $transaction->fresh()->whatsappReadyUrl();

            if ($waUrl) {
                return redirect()->away($waUrl);
            }

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('error', 'Nomor WhatsApp pelanggan belum tersedia.');
        }

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', $successMessage);
    }

    public function show($id)
    {
        $transaction = Transaction::with([
            'member',
            'cashier',
            'outlet',
            'selfServiceDetails.machine',
            'selfServiceDetails.machineDuration',
            'servicePackages',
            'addonOptions',
            'items'
        ])->findOrFail($id);
        $this->authorize('view', $transaction);

        return view('pages.transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        DB::transaction(function () use ($transaction) {
            if ($transaction->transaction_type === 'SELF_SERVICE' && $transaction->status === 'IN_PROGRESS') {
                foreach ($transaction->selfServiceDetails as $detail) {
                    if ($detail->machine) {
                        $detail->machine->update(['status' => 'AVAILABLE']);
                    }
                }
            }
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
