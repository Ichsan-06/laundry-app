<?php

namespace App\Http\Controllers;

use App\Models\AddonOption;
use Illuminate\Http\Request;
use App\Services\TenantContextService;

class AddonController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = AddonOption::query();
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

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

        $outletId = $request->user()->isOwner()
            ? \App\Models\Outlet::query()->where('tenant_id', $request->user()->tenant_id)->orderBy('nama_outlet')->value('id')
            : $request->user()->outlet_id;

        AddonOption::create([
            'outlet_id' => $outletId,
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('addons.index')->with('success', 'Addon created successfully.');
    }

    public function edit(AddonOption $addon)
    {
        abort_if(! in_array($addon->outlet_id, auth()->user()->accessibleOutletIds(), true), 403);
        return view('pages.addons.edit', compact('addon'));
    }

    public function update(Request $request, AddonOption $addon)
    {
        abort_if(! in_array($addon->outlet_id, $request->user()->accessibleOutletIds(), true), 403);

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
        abort_if(! in_array($addon->outlet_id, auth()->user()->accessibleOutletIds(), true), 403);
        $addon->delete();
        return redirect()->route('addons.index')->with('success', 'Addon deleted successfully.');
    }
}
