<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\Outlet;
use App\Services\TenantContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = InventoryItem::with(['outlet', 'stockMovements.user']);
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('stok', '<=', 'alert_stok');
            }

            if ($request->status === 'safe') {
                $query->whereColumn('stok', '>', 'alert_stok');
            }
        }

        if ($request->filled('outlet_id') && $request->outlet_id !== 'all') {
            $query->where('outlet_id', $request->outlet_id);
        }

        $inventories = $query->orderBy('nama_barang')->paginate(15)->withQueryString();

        $statsQuery = InventoryItem::query();
        $statsQuery = $this->tenantContextService->scopeByUser($statsQuery, $request->user());

        $stats = [
            'total_items' => (clone $statsQuery)->count(),
            'low_stock_count' => (clone $statsQuery)->whereColumn('stok', '<=', 'alert_stok')->count(),
            'safe_stock_count' => (clone $statsQuery)->whereColumn('stok', '>', 'alert_stok')->count(),
        ];

        $outlets = $request->user()->isSuperAdmin()
            ? Outlet::query()->orderBy('nama_outlet')->get()
            : Outlet::query()->whereIn('id', $request->user()->accessibleOutletIds())->orderBy('nama_outlet')->get();

        return view('pages.inventories.index', [
            'inventories' => $inventories,
            'stats' => $stats,
            'outlets' => $outlets,
            'unitOptions' => InventoryItem::UNIT_OPTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|in:' . implode(',', array_keys(InventoryItem::UNIT_OPTIONS)),
            'stok_awal' => 'nullable|numeric|min:0',
            'alert_stok' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        abort_if(! in_array($validated['outlet_id'], $request->user()->accessibleOutletIds(), true), 403);

        DB::transaction(function () use ($validated, $request) {
            $stokAwal = (float) ($validated['stok_awal'] ?? 0);

            $item = InventoryItem::create([
                'outlet_id' => $validated['outlet_id'],
                'nama_barang' => $validated['nama_barang'],
                'satuan' => $validated['satuan'],
                'stok' => $stokAwal,
                'alert_stok' => $validated['alert_stok'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            if ($stokAwal > 0) {
                InventoryStockMovement::create([
                    'inventory_item_id' => $item->id,
                    'outlet_id' => $item->outlet_id,
                    'user_id' => $request->user()?->id,
                    'type' => InventoryStockMovement::TYPE_IN,
                    'quantity' => $stokAwal,
                    'stock_before' => 0,
                    'stock_after' => $stokAwal,
                    'catatan' => 'Stok awal barang',
                ]);
            }
        });

        return redirect()->route('inventories.index')->with('success', 'Barang inventaris berhasil ditambahkan.');
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        abort_if(! in_array($inventory->outlet_id, $request->user()->accessibleOutletIds(), true), 403);

        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|in:' . implode(',', array_keys(InventoryItem::UNIT_OPTIONS)),
            'alert_stok' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        abort_if(! in_array($validated['outlet_id'], $request->user()->accessibleOutletIds(), true), 403);

        $inventory->update([
            'outlet_id' => $validated['outlet_id'],
            'nama_barang' => $validated['nama_barang'],
            'satuan' => $validated['satuan'],
            'alert_stok' => $validated['alert_stok'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('inventories.index')->with('success', 'Barang inventaris berhasil diperbarui.');
    }

    public function destroy(Request $request, InventoryItem $inventory)
    {
        abort_if(! in_array($inventory->outlet_id, $request->user()->accessibleOutletIds(), true), 403);

        $inventory->delete();

        return redirect()->route('inventories.index')->with('success', 'Barang inventaris berhasil dihapus.');
    }

    public function restock(Request $request, InventoryItem $inventory)
    {
        abort_if(! in_array($inventory->outlet_id, $request->user()->accessibleOutletIds(), true), 403);

        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($inventory, $validated, $request) {
            $inventory->refresh();
            $before = (float) $inventory->stok;
            $quantity = (float) $validated['jumlah'];
            $after = $before + $quantity;

            $inventory->update(['stok' => $after]);

            InventoryStockMovement::create([
                'inventory_item_id' => $inventory->id,
                'outlet_id' => $inventory->outlet_id,
                'user_id' => $request->user()?->id,
                'type' => InventoryStockMovement::TYPE_IN,
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        return redirect()->route('inventories.index')->with('success', 'Stok barang berhasil ditambahkan.');
    }

    public function use(Request $request, InventoryItem $inventory)
    {
        abort_if(! in_array($inventory->outlet_id, $request->user()->accessibleOutletIds(), true), 403);

        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($inventory, $validated, $request) {
            $inventory->refresh();
            $before = (float) $inventory->stok;
            $quantity = (float) $validated['jumlah'];

            if ($quantity > $before) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah pemakaian melebihi stok tersedia.',
                ]);
            }

            $after = $before - $quantity;

            $inventory->update(['stok' => $after]);

            InventoryStockMovement::create([
                'inventory_item_id' => $inventory->id,
                'outlet_id' => $inventory->outlet_id,
                'user_id' => $request->user()?->id,
                'type' => InventoryStockMovement::TYPE_OUT,
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        return redirect()->route('inventories.index')->with('success', 'Pemakaian stok berhasil dicatat.');
    }
}
