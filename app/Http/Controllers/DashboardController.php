<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\SelfServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use App\Services\TenantContextService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        $user = $request->user();
        $transactions = $this->tenantContextService->scopeByUser(Transaction::query(), $user);
        $machines = $this->tenantContextService->scopeByUser(Machine::query(), $user);
        $members = $this->tenantContextService->scopeByUser(Member::query(), $user);

        // 1. Summary Cards (Filtered by date)
        $totalRevenue = (clone $transactions)->where('status', 'COMPLETED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalMachines = (clone $machines)->count();
        $activeMachinesCount = (clone $machines)->where('status', 'RUNNING')->count();
        $machineCapacityPercent = $totalMachines > 0 ? round(($activeMachinesCount / $totalMachines) * 100) : 0;

        $pendingOrders = (clone $transactions)->whereIn('status', ['PENDING', 'IN_PROGRESS'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $newMembersCount = (clone $members)->whereBetween('created_at', [$startDate, $endDate])->count();

        // 2. Live Machines (Always current status)
        $liveMachines = (clone $machines)->get()->map(function($machine) {
            $status = $machine->status;
            $timeLeft = null;
            
            if ($status === 'RUNNING') {
                $detail = SelfServiceDetail::where('machine_id', $machine->id)
                    ->where('end_time', '>', Carbon::now())
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($detail) {
                    $timeLeft = Carbon::now()->diffInMinutes($detail->end_time, false);
                    if ($timeLeft < 0) $timeLeft = 0;
                }
            }

            return [
                'code' => $machine->machine_code,
                'type' => $machine->machine_type,
                'status' => $status,
                'time_left' => $timeLeft ? $timeLeft . 'm left' : ($status === 'READY' ? 'Ready' : 'Service'),
                'progress' => $timeLeft ? (1 - ($timeLeft / 60)) * 100 : 0
            ];
        })->take(3);

        // 3. Revenue Over Time (Daily for the selected range)
        $chartLabels = [];
        $chartData = [];
        
        $diffDays = $startDate->diffInDays($endDate);
        // Limit chart to 30 points for performance
        $step = $diffDays > 30 ? ceil($diffDays / 30) : 1;

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDays($step)) {
            $chartLabels[] = $date->format('d M');
            $chartData[] = $this->tenantContextService->scopeByUser(Transaction::query(), $user)->whereDate('created_at', $date)
                ->where('status', 'COMPLETED')
                ->sum('total_amount');
        }

        // 4. Recent Transactions (Within range)
        $recentTransactions = $this->tenantContextService->scopeByUser(Transaction::with(['member']), $user)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($t) {
                return [
                    'id' => substr($t->transaction_number, -4),
                    'customer' => $t->member->nama ?? 'Guest',
                    'customer_initials' => strtoupper(substr($t->member->nama ?? 'G', 0, 2)),
                    'service' => str_replace('_', ' ', $t->service_type),
                    'status' => $t->status,
                    'amount' => $t->total_amount
                ];
            });

        return view('pages.dashboard.index', compact(
            'totalRevenue',
            'activeMachinesCount',
            'totalMachines',
            'machineCapacityPercent',
            'pendingOrders',
            'newMembersCount',
            'liveMachines',
            'chartLabels',
            'chartData',
            'recentTransactions',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $transactions = $this->tenantContextService->scopeByUser(Transaction::with(['member', 'cashier']), $request->user())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $csvFileName = 'laundry_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Transaction ID', 'Date', 'Customer', 'Service', 'Status', 'Total Amount', 'Payment Method'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->transaction_number,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->member->nama ?? 'Guest',
                    $t->service_type,
                    $t->status,
                    $t->total_amount,
                    $t->payment_method,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
