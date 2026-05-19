<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Get transactions grouped by day for a given month
     */
    public function getMonthTransactions(int $year, int $month, ?string $outletId = null, array $filters = []): Collection
    {
        $query = Transaction::query();

        // Filter by month and year
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->clone()->endOfMonth()->endOfDay();

        $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        // Filter by outlet if provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Apply filters
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'TERLAMBAT') {
                // Terlambat: created on previous date, not yet completed
                $yesterday = Carbon::yesterday()->endOfDay();
                $query->where('created_at', '<=', $yesterday)
                      ->whereNotIn('status', ['COMPLETED', 'CANCELLED']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['service_type']) && $filters['service_type'] !== 'all') {
            $query->where('service_type', $filters['service_type']);
        }

        // Load relationships
        $query->with(['member', 'outlet', 'servicePackages'])
              ->orderBy('created_at', 'asc');

        $transactions = $query->get();

        // Group by date
        return $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        });
    }

    /**
     * Get transactions for a specific day
     */
    public function getTransactionsByDay(string $date, ?string $outletId = null, array $filters = []): Collection
    {
        $query = Transaction::query();

        // Filter by exact day
        $dayStart = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $dayEnd = $dayStart->clone()->endOfDay();

        $query->whereBetween('created_at', [$dayStart, $dayEnd]);

        // Filter by outlet if provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Apply filters
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'TERLAMBAT') {
                // Terlambat: created on previous date, not yet completed
                $yesterday = Carbon::yesterday()->endOfDay();
                $query->where('created_at', '<=', $yesterday)
                      ->whereNotIn('status', ['COMPLETED', 'CANCELLED']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['service_type']) && $filters['service_type'] !== 'all') {
            $query->where('service_type', $filters['service_type']);
        }

        // Load relationships
        $query->with(['member', 'outlet', 'servicePackages'])
              ->orderBy('created_at', 'desc');

        return $query->get();
    }

    /**
     * Calculate statistics for a given time period
     */
    public function calculateStats(int $year, int $month, ?string $outletId = null, array $filters = []): array
    {
        $query = Transaction::query();

        // Filter by month and year
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->clone()->endOfMonth()->endOfDay();

        $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        // Filter by outlet if provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Apply service type filter only (status filters are calculated separately)
        if (!empty($filters['service_type']) && $filters['service_type'] !== 'all') {
            $query->where('service_type', $filters['service_type']);
        }

        // Base stats
        $totalOrders = (clone $query)->count();

        // Terlambat: created on previous date, not yet completed
        $terlambat = (clone $query)
            ->where('created_at', '<=', Carbon::yesterday()->endOfDay())
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED'])
            ->count();

        // Siap Diambil (Ready): status = READY
        $ready = (clone $query)
            ->where('status', 'READY')
            ->count();

        // Pending Pickup (awaiting pickup - self service ready)
        $pendingPickup = (clone $query)
            ->where('status', 'READY')
            ->where('transaction_type', 'SELF_SERVICE')
            ->count();

        // Pending Delivery (drop off ready for customer pickup)
        $pendingDelivery = (clone $query)
            ->where('status', 'READY')
            ->where('transaction_type', 'DROP_OFF')
            ->count();

        return [
            'total_orders' => $totalOrders,
            'terlambat' => $terlambat,
            'siap_diambil' => $ready,
            'pending_pickup' => $pendingPickup,
            'pending_delivery' => $pendingDelivery,
        ];
    }

    /**
     * Determine if a transaction is late
     */
    public function isLateTransaction(Transaction $transaction): bool
    {
        if ($transaction->status === 'COMPLETED' || $transaction->status === 'CANCELLED') {
            return false;
        }

        return $transaction->created_at < Carbon::yesterday()->endOfDay();
    }

    /**
     * Get status to color mapping
     */
    public function getStatusColors(): array
    {
        return [
            'PENDING' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-600',
                'ring' => 'ring-amber-100',
                'dot' => 'bg-amber-400',
            ],
            'IN_PROGRESS' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-600',
                'ring' => 'ring-blue-100',
                'dot' => 'bg-blue-400',
            ],
            'READY' => [
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-600',
                'ring' => 'ring-indigo-100',
                'dot' => 'bg-indigo-400',
            ],
            'COMPLETED' => [
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
                'ring' => 'ring-emerald-100',
                'dot' => 'bg-emerald-400',
            ],
            'CANCELLED' => [
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-600',
                'ring' => 'ring-rose-100',
                'dot' => 'bg-rose-400',
            ],
            'TERLAMBAT' => [
                'bg' => 'bg-red-50',
                'text' => 'text-red-600',
                'ring' => 'ring-red-100',
                'dot' => 'bg-red-400',
            ],
        ];
    }

    /**
     * Get status label mapping
     */
    public function getStatusLabels(): array
    {
        return [
            'PENDING' => 'Menunggu',
            'IN_PROGRESS' => 'Diproses',
            'READY' => 'Siap Diambil',
            'COMPLETED' => 'Selesai',
            'CANCELLED' => 'Dibatalkan',
            'TERLAMBAT' => 'Terlambat',
        ];
    }

    /**
     * Get service type label mapping
     */
    public function getServiceTypeLabels(): array
    {
        return [
            'WASH_ONLY' => 'Cuci Saja',
            'DRY_ONLY' => 'Kering Saja',
            'WASH_DRY' => 'Cuci & Kering',
            'IRONING' => 'Setrika',
            'COMPLETE' => 'Komplit',
        ];
    }

    /**
     * Get transaction type label mapping
     */
    public function getTransactionTypeLabels(): array
    {
        return [
            'SELF_SERVICE' => 'Mandiri',
            'DROP_OFF' => 'Drop Off',
        ];
    }

    /**
     * Count transactions by status for a specific day
     */
    public function countByStatusForDay(string $date, ?string $outletId = null): array
    {
        $dayStart = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $dayEnd = $dayStart->clone()->endOfDay();

        $query = Transaction::whereBetween('created_at', [$dayStart, $dayEnd]);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        // Count by status
        $counts = [
            'PENDING' => 0,
            'IN_PROGRESS' => 0,
            'READY' => 0,
            'COMPLETED' => 0,
            'CANCELLED' => 0,
            'TERLAMBAT' => 0,
        ];

        foreach ($counts as $status => &$count) {
            if ($status === 'TERLAMBAT') {
                // For TERLAMBAT from today, we still show 0 as these are from previous days
                // But we can still calculate it for today if needed
                $count = 0;
            } else {
                $count = (clone $query)->where('status', $status)->count();
            }
        }

        return $counts;
    }
}
