<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TenantContextService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function getDueTransactions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Ambil transaksi DROP_OFF yang belum selesai (status bukan COMPLETED dan READY)
        // dan waktu estimasi selesai sudah lewat atau dalam waktu 6 jam ke depan.
        $now = Carbon::now();
        $dueThreshold = $now->copy()->addHours(6);

        $transactions = $this->tenantContextService
            ->scopeByUser(Transaction::query(), $user)
            ->with(['member', 'outlet'])
            ->where('transaction_type', 'DROP_OFF')
            ->whereNotIn('status', ['COMPLETED', 'READY'])
            ->whereNotNull('estimated_finish')
            ->where('estimated_finish', '<=', $dueThreshold)
            ->orderBy('estimated_finish', 'asc')
            ->get();

        $items = $transactions->map(function ($t) use ($now) {
            $finish = Carbon::parse($t->estimated_finish);
            $isOverdue = $finish->isPast();
            
            // Format perbedaan waktu yang manusiawi dalam Bahasa Indonesia
            $diffString = $finish->diffForHumans($now);

            return [
                'id' => $t->id,
                'transaction_number' => $t->transaction_number,
                'member_name' => $t->member?->nama ?? 'Pelanggan Umum',
                'outlet_name' => $t->outlet?->nama_outlet ?? '-',
                'estimated_finish' => $finish->translatedFormat('d M Y H:i'),
                'is_overdue' => $isOverdue,
                'time_left' => $diffString,
                'url' => route('transactions.show', $t->id),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $items->count(),
            'items' => $items,
        ]);
    }
}
