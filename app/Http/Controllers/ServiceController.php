<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ServicePackage::query();

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nama_paket', 'like', "%{$search}%");
        }

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('aktif', $request->status == 'active');
        }

        $services = $query->orderBy('nama_paket', 'asc')->paginate(10);

        return view('pages.services.index', compact('services'));
    }

    public function create()
    {
        return view('pages.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_per_kg' => 'required|numeric|min:0',
            'berat_minimal' => 'required|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        ServicePackage::create([
            'outlet_id' => \App\Models\Outlet::first()->id,
            'nama_paket' => $validated['nama_paket'],
            'deskripsi' => $validated['deskripsi'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'berat_minimal' => $validated['berat_minimal'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service package created successfully.');
    }

    public function edit(ServicePackage $service)
    {
        return view('pages.services.edit', compact('service'));
    }

    public function update(Request $request, ServicePackage $service)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_per_kg' => 'required|numeric|min:0',
            'berat_minimal' => 'required|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        $service->update([
            'nama_paket' => $validated['nama_paket'],
            'deskripsi' => $validated['deskripsi'],
            'harga_per_kg' => $validated['harga_per_kg'],
            'berat_minimal' => $validated['berat_minimal'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service package updated successfully.');
    }

    public function destroy(ServicePackage $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service package deleted successfully.');
    }
}
