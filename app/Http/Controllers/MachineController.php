<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $query = Machine::query();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->get('search');
            $query->where('machine_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        $machines = $query->with('durations')->orderBy('machine_code', 'asc')->get();

        // Statistics
        $stats = [
            'available' => Machine::where('status', 'AVAILABLE')->count(),
            'in_use' => Machine::where('status', 'IN_USE')->count(),
            'maintenance' => Machine::whereIn('status', ['MAINTENANCE', 'FAULTY'])->count(),
            'today_revenue' => Transaction::where('status', 'COMPLETED')
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

        $machine = Machine::create([
            'outlet_id' => \App\Models\Outlet::first()->id, // Assuming single outlet for now
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
        $machine->load('durations');
        return view('pages.machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
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
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Machine deleted successfully.');
    }
}
