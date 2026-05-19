<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\SelfServiceDetail;
use App\Models\Outlet;
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

    private function getReportData(Request $request)
    {
        $filter = $request->get('filter', 'monthly'); // monthly, weekly, daily
        $now = Carbon::now();
        $user = $request->user();
        
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        
        if ($filter === 'weekly') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($filter === 'daily') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
        }

        $transactionQuery = $this->tenantContextService->scopeByUser(Transaction::query(), $user);

        // Calculate Outlet Name
        $outletName = '';
        if ($user->outlet_id) {
            $outletName = $user->outlet?->nama_outlet;
        } else {
            $outletIds = $this->tenantContextService->accessibleOutletIds($user);
            if (count($outletIds) === 1) {
                $outletName = Outlet::find($outletIds[0])?->nama_outlet;
            } else {
                $outletName = $user->tenant?->name ?? 'Semua Outlet';
            }
        }

        // Total Omzet
        $totalRevenue = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');

        // Order selesai
        $completedOrdersCount = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->where('status', 'COMPLETED')->count();

        // Layanan Terlaris
        $popularServiceRow = (clone $transactionQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->first();
        
        $serviceNames = [
            'WASH_ONLY' => 'Cuci Saja',
            'DRY_ONLY' => 'Kering Saja',
            'WASH_DRY' => 'Cuci Kering',
            'IRONING' => 'Setrika',
            'COMPLETE' => 'Lengkap (Cuci + Kering + Setrika)'
        ];

        $popularService = $popularServiceRow ? ($serviceNames[$popularServiceRow->service_type] ?? str_replace('_', ' ', $popularServiceRow->service_type)) . ' (' . $popularServiceRow->count . ' order)' : 'Tidak ada transaksi';

        // Mesin Paling banyak terpakai
        $mostUsedMachineRow = DB::table('self_service_details')
            ->join('machines', 'self_service_details.machine_id', '=', 'machines.id')
            ->whereIn('machines.outlet_id', $this->tenantContextService->accessibleOutletIds($user))
            ->whereBetween('self_service_details.created_at', [$startDate, $endDate])
            ->select('machines.machine_code', 'machines.machine_type', DB::raw('count(*) as count'))
            ->groupBy('machines.id', 'machines.machine_code', 'machines.machine_type')
            ->orderByDesc('count')
            ->first();

        $mostUsedMachine = $mostUsedMachineRow ? $mostUsedMachineRow->machine_code . ' - ' . $mostUsedMachineRow->machine_type . ' (' . $mostUsedMachineRow->count . ' siklus)' : 'Tidak ada penggunaan mesin';

        // Metode pembayaran
        $qrisTotal = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->where('payment_method', 'QRIS')->sum('total_amount');
        $cashTotal = (clone $transactionQuery)->whereBetween('created_at', [$startDate, $endDate])->where('payment_method', 'CASH')->sum('total_amount');

        $periodString = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y') . ' (' . ucfirst($filter) . ')';

        return [
            'filter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'outletName' => $outletName,
            'totalRevenue' => $totalRevenue,
            'completedOrdersCount' => $completedOrdersCount,
            'popularService' => $popularService,
            'mostUsedMachine' => $mostUsedMachine,
            'qrisTotal' => $qrisTotal,
            'cashTotal' => $cashTotal,
            'periodString' => $periodString,
        ];
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.reports.pdf', $data);
        
        return $pdf->download('laporan-laundry-' . $data['filter'] . '-' . now()->format('YmdHis') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="laporan-laundry-' . $data['filter'] . '-' . now()->format('YmdHis') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['LAPORAN KINERJA LAUNDRY - WASHKITA']);
            fputcsv($file, []);
            
            fputcsv($file, ['Parameter', 'Nilai']);
            fputcsv($file, ['Periode', $data['periodString']]);
            fputcsv($file, ['Outlet', $data['outletName']]);
            fputcsv($file, ['Total Omzet', 'Rp ' . number_format($data['totalRevenue'], 0, ',', '.')]);
            fputcsv($file, ['Order Selesai', number_format($data['completedOrdersCount'], 0, ',', '.')]);
            fputcsv($file, ['Layanan Terlaris', $data['popularService']]);
            fputcsv($file, ['Mesin Paling Banyak Terpakai', $data['mostUsedMachine']]);
            
            fputcsv($file, []);
            fputcsv($file, ['METODE PEMBAYARAN', '']);
            fputcsv($file, ['QRIS Total', 'Rp ' . number_format($data['qrisTotal'], 0, ',', '.')]);
            fputcsv($file, ['Cash Total', 'Rp ' . number_format($data['cashTotal'], 0, ',', '.')]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
