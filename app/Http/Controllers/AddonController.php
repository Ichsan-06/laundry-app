<?php

namespace App\Http\Controllers;

use App\Models\AddonOption;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index(Request $request)
    {
        $query = AddonOption::query();

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('aktif', $request->status == 'active');
        }

        $addons = $query->orderBy('nama', 'asc')->paginate(10);

        return view('pages.addons.index', compact('addons'));
    }

    public function create()
    {
        return view('pages.addons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        AddonOption::create([
            'outlet_id' => \App\Models\Outlet::first()->id,
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('addons.index')->with('success', 'Addon created successfully.');
    }

    public function edit(AddonOption $addon)
    {
        return view('pages.addons.edit', compact('addon'));
    }

    public function update(Request $request, AddonOption $addon)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        $addon->update([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('addons.index')->with('success', 'Addon updated successfully.');
    }

    public function destroy(AddonOption $addon)
    {
        $addon->delete();
        return redirect()->route('addons.index')->with('success', 'Addon deleted successfully.');
    }
}
