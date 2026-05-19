<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Services\CalendarService;
use App\Services\TenantContextService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CalendarController extends Controller
{
    public function __construct(
        private readonly CalendarService $calendarService,
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        // Authorization check
        abort_unless($request->user()?->can('calendar.view'), 403);

        $user = $request->user();
        
        // Get accessible outlets for user
        $accessibleOutletIds = $this->tenantContextService->accessibleOutletIds($user);
        $outlets = Outlet::whereIn('id', $accessibleOutletIds)->get();

        // Determine current outlet
        $selectedOutletId = null;
        if ($user->isSuperAdmin() || $outlets->count() > 1) {
            // Owner or admin - use selected outlet from request or first outlet
            $selectedOutletId = $request->get('outlet_id') ?? $outlets->first()?->id;
        } else {
            // Cashier - use their outlet
            $selectedOutletId = $outlets->first()?->id;
        }

        // Parse month and year from request or use current
        $now = Carbon::now();
        $year = $request->get('year', $now->year);
        $month = $request->get('month', $now->month);

        // Validate year and month
        $year = (int) $year;
        $month = (int) $month;
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }
        if ($year < 2000 || $year > 2099) {
            $year = $now->year;
        }

        // Build filters array
        $filters = [
            'status' => $request->get('status', 'all'),
            'service_type' => $request->get('service_type', 'all'),
        ];

        // Get month transactions grouped by day
        $transactionsByDay = $this->calendarService->getMonthTransactions(
            $year,
            $month,
            $selectedOutletId,
            $filters
        );

        // Calculate statistics
        $stats = $this->calendarService->calculateStats(
            $year,
            $month,
            $selectedOutletId,
            $filters
        );

        // Get mapping data
        $statusColors = $this->calendarService->getStatusColors();
        $statusLabels = $this->calendarService->getStatusLabels();
        $serviceTypeLabels = $this->calendarService->getServiceTypeLabels();
        $transactionTypeLabels = $this->calendarService->getTransactionTypeLabels();

        // Get status options for filter
        $statusOptions = [
            'all' => 'Semua Status',
            'PENDING' => $statusLabels['PENDING'],
            'IN_PROGRESS' => $statusLabels['IN_PROGRESS'],
            'READY' => $statusLabels['READY'],
            'COMPLETED' => $statusLabels['COMPLETED'],
            'CANCELLED' => $statusLabels['CANCELLED'],
            'TERLAMBAT' => $statusLabels['TERLAMBAT'],
        ];

        // Get service type options
        $serviceTypeOptions = ['all' => 'Semua Layanan'] + $serviceTypeLabels;

        // Create calendar grid data
        $currentDate = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Build calendar data
        $calendarData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $calendarData[$day] = [
                'date' => $dateStr,
                'transactions' => $transactionsByDay->get($dateStr, collect()),
                'counts' => $this->calendarService->countByStatusForDay($dateStr, $selectedOutletId),
            ];
        }

        return view('pages.calendar.index', [
            'year' => $year,
            'month' => $month,
            'currentDate' => $currentDate,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'calendarData' => $calendarData,
            'stats' => $stats,
            'statusColors' => $statusColors,
            'statusLabels' => $statusLabels,
            'serviceTypeLabels' => $serviceTypeLabels,
            'transactionTypeLabels' => $transactionTypeLabels,
            'statusOptions' => $statusOptions,
            'serviceTypeOptions' => $serviceTypeOptions,
            'outlets' => $outlets,
            'selectedOutletId' => $selectedOutletId,
            'selectedStatus' => $filters['status'],
            'selectedServiceType' => $filters['service_type'],
            'transactionsByDay' => $transactionsByDay,
        ]);
    }

    /**
     * Get transactions for a specific day (AJAX endpoint)
     */
    public function show(Request $request, string $date): JsonResponse
    {
        // Authorization check
        abort_unless($request->user()?->can('calendar.view'), 403);

        $user = $request->user();
        $accessibleOutletIds = $this->tenantContextService->accessibleOutletIds($user);

        // Validate date format
        try {
            Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Get selected outlet
        $selectedOutletId = $request->get('outlet_id');
        if ($selectedOutletId && !in_array($selectedOutletId, $accessibleOutletIds)) {
            return response()->json(['error' => 'Unauthorized outlet'], 403);
        }

        // If no outlet selected, use first accessible
        if (!$selectedOutletId) {
            $selectedOutletId = $accessibleOutletIds[0] ?? null;
        }

        // Build filters
        $filters = [
            'status' => $request->get('status', 'all'),
            'service_type' => $request->get('service_type', 'all'),
        ];

        // Get transactions for the day
        $transactions = $this->calendarService->getTransactionsByDay(
            $date,
            $selectedOutletId,
            $filters
        );

        // Get mapping data
        $statusLabels = $this->calendarService->getStatusLabels();
        $serviceTypeLabels = $this->calendarService->getServiceTypeLabels();
        $transactionTypeLabels = $this->calendarService->getTransactionTypeLabels();
        $statusColors = $this->calendarService->getStatusColors();

        // Format response
        $formattedTransactions = $transactions->map(function ($transaction) use (
            $statusLabels,
            $serviceTypeLabels,
            $transactionTypeLabels,
            $statusColors
        ) {
            $status = $transaction->status;
            if ($this->calendarService->isLateTransaction($transaction)) {
                $status = 'TERLAMBAT';
            }

            return [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'member_name' => $transaction->member?->nama ?? 'Unknown',
                'status' => $status,
                'status_label' => $statusLabels[$status] ?? $status,
                'status_color' => $statusColors[$status] ?? [],
                'service_type' => $transaction->service_type,
                'service_type_label' => $serviceTypeLabels[$transaction->service_type] ?? $transaction->service_type,
                'transaction_type' => $transaction->transaction_type,
                'transaction_type_label' => $transactionTypeLabels[$transaction->transaction_type] ?? $transaction->transaction_type,
                'created_at' => $transaction->created_at->format('H:i'),
                'total_amount' => number_format($transaction->total_amount, 2),
                'detail_url' => route('transactions.show', $transaction->id),
            ];
        });

        return response()->json([
            'date' => $date,
            'transactions' => $formattedTransactions,
            'count' => $transactions->count(),
        ]);
    }
}
