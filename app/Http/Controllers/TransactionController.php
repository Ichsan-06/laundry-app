<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['member', 'cashier']);

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

        // Filter by Service Type
        if ($request->has('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }

        // Filter by Payment Method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
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

        // Statistics
        $stats = [
            'total_revenue' => Transaction::where('status', 'COMPLETED')->sum('total_amount'),
            'total_orders' => Transaction::count(),
            'active_orders' => Transaction::whereIn('status', ['PENDING', 'IN_PROGRESS'])->count(),
            'completed_today' => Transaction::where('status', 'COMPLETED')
                                            ->whereDate('created_at', now()->toDateString())
                                            ->count(),
        ];

        // For modals
        $members = Member::all();
        $outlets = Outlet::all();
        $cashiers = User::all();

        return view('pages.transactions.index', compact('transactions', 'stats', 'members', 'outlets', 'cashiers'));
    }

    public function store(Request $request)
    {
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
        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'payment_method' => 'required|in:CASH,TRANSFER,E_WALLET,QRIS',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
