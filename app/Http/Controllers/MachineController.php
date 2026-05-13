<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\TenantContextService;

class MachineController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = Machine::query();
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->get('search');
            $query->where('machine_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        $machines = $query->with('durations')->orderBy('machine_code', 'asc')->get();

        // Statistics
        $machineStatsQuery = $this->tenantContextService->scopeByUser(Machine::query(), $request->user());
        $transactionStatsQuery = $this->tenantContextService->scopeByUser(Transaction::query(), $request->user());

        $stats = [
            'available' => (clone $machineStatsQuery)->where('status', 'AVAILABLE')->count(),
            'in_use' => (clone $machineStatsQuery)->where('status', 'IN_USE')->count(),
            'maintenance' => (clone $machineStatsQuery)->whereIn('status', ['MAINTENANCE', 'FAULTY'])->count(),
            'today_revenue' => (clone $transactionStatsQuery)->where('status', 'COMPLETED')
                                          ->whereDate('created_at', now()->toDateString())
                                          ->sum('total_amount'),
        ];

        return view('pages.machines.index', compact('machines', 'stats'));
    }
    public function create()
    {
        return view('pages.machines.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Machine::class);

        $validated = $request->validate([
            'machine_code' => 'required|unique:machines,machine_code',
            'machine_type' => 'required|in:WASHER,DRYER',
            'brand' => 'nullable|string',
            'capacity_kg' => 'required|numeric',
            'status' => 'required|in:AVAILABLE,IN_USE,MAINTENANCE,FAULTY',
            'durations' => 'required|array',
            'durations.*.price' => 'required|numeric',
            'durations.*.duration_minutes' => 'required|numeric',
        ]);

        $outletId = $request->user()->isOwner()
            ? \App\Models\Outlet::query()->where('tenant_id', $request->user()->tenant_id)->orderBy('nama_outlet')->value('id')
            : $request->user()->outlet_id;

        $machine = Machine::create([
            'outlet_id' => $outletId,
            'machine_code' => $validated['machine_code'],
            'machine_type' => $validated['machine_type'],
            'brand' => $validated['brand'],
            'capacity_kg' => $validated['capacity_kg'],
            'status' => $validated['status'],
        ]);

        foreach ($validated['durations'] as $type => $data) {
            $machine->durations()->create([
                'duration_type' => $type,
                'duration_minutes' => $data['duration_minutes'],
                'price' => $data['price'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('machines.index')->with('success', 'Machine created successfully.');
    }

    public function edit(Machine $machine)
    {
        $this->authorize('update', $machine);
        $machine->load('durations');
        return view('pages.machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $this->authorize('update', $machine);

        $validated = $request->validate([
            'machine_code' => 'required|unique:machines,machine_code,' . $machine->id,
            'machine_type' => 'required|in:WASHER,DRYER',
            'brand' => 'nullable|string',
            'capacity_kg' => 'required|numeric',
            'status' => 'required|in:AVAILABLE,IN_USE,MAINTENANCE,FAULTY',
            'durations' => 'required|array',
            'durations.*.price' => 'required|numeric',
            'durations.*.duration_minutes' => 'required|numeric',
        ]);

        $machine->update([
            'machine_code' => $validated['machine_code'],
            'machine_type' => $validated['machine_type'],
            'brand' => $validated['brand'],
            'capacity_kg' => $validated['capacity_kg'],
            'status' => $validated['status'],
        ]);

        foreach ($validated['durations'] as $type => $data) {
            $machine->durations()->updateOrCreate(
                ['duration_type' => $type],
                [
                    'duration_minutes' => $data['duration_minutes'],
                    'price' => $data['price'],
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('machines.index')->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        $this->authorize('delete', $machine);
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Machine deleted successfully.');
    }
}
