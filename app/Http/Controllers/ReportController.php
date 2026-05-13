<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\SelfServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\TenantContextService;

class ReportController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'monthly'); // monthly, weekly, daily
        $now = Carbon::now();
        $user = $request->user();
        
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        $format = 'd M';
        $groupBy = 'date';

        if ($filter === 'weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($filter === 'daily') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $format = 'H:i';
            $groupBy = 'hour';
        }

        // 1. Summary Cards (Calculated based on the selected range)
        $transactionQuery = $this->tenantContextService->scopeByUser(Transaction::query(), $user);
        $machineQuery = $this->tenantContextService->scopeByUser(Machine::query(), $user);
        $memberQuery = $this->tenantContextService->scopeByUser(Member::query(), $user);

        $totalRevenue = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalTransactions = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->count();
        $newMembers = (clone $memberQuery)->whereBetween('created_at', [$startDate, $endDate])->count();

        // Efficiency calculation
        $totalMachines = (clone $machineQuery)->count();
        $activeMachines = (clone $machineQuery)->where('status', 'READY')->count();
        $avgEfficiency = $totalMachines > 0 ? ($activeMachines / $totalMachines) * 100 : 0;

        // 2. Chart Data
        $chartLabels = [];
        $chartData = [];

        if ($filter === 'daily') {
            $hourlyRevenue = $this->tenantContextService->scopeByUser(Transaction::query(), $user)->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('hour')
            ->get()
            ->pluck('total', 'hour');

            for ($i = 0; $i < 24; $i++) {
                $chartLabels[] = sprintf('%02d:00', $i);
                $chartData[] = $hourlyRevenue[$i] ?? 0;
            }
        } else {
            $dailyRevenue = $this->tenantContextService->scopeByUser(Transaction::query(), $user)->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $chartLabels[] = $date->format('d M');
                $chartData[] = $dailyRevenue[$dateStr] ?? 0;
            }
        }

        // 3. Service Usage Breakdown
        $serviceUsage = $this->tenantContextService->scopeByUser(Transaction::query(), $user)->select('service_type', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('service_type')
            ->get()
            ->map(function ($item) use ($totalTransactions) {
                return [
                    'name' => $item->service_type,
                    'count' => $item->count,
                    'percentage' => $totalTransactions > 0 ? round(($item->count / $totalTransactions) * 100) : 0
                ];
            });

        // 4. Machine Statistics
        $machineStats = (clone $machineQuery)->get()->map(function ($machine) use ($startDate, $endDate) {
            $details = SelfServiceDetail::where('machine_id', $machine->id)
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            return [
                'code' => $machine->machine_code,
                'type' => $machine->machine_type,
                'cycles' => $details->count(),
                'avg_duration' => round($details->avg('duration_minutes') ?? 0),
                'status' => $machine->status,
                'revenue' => $details->sum('price')
            ];
        });

        return view('pages.reports.index', compact(
            'totalRevenue',
            'totalTransactions',
            'newMembers',
            'avgEfficiency',
            'chartLabels',
            'chartData',
            'serviceUsage',
            'machineStats',
            'filter',
            'startDate',
            'endDate'
        ));
    }
}
