<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use App\Models\Outlet;
use App\Services\TenantContextService;
use Illuminate\Http\Request;

class OutcomeController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = Outcome::query()->with(['outlet', 'user']);
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('outlet_id') && $request->outlet_id !== 'all') {
            $query->where('outlet_id', $request->outlet_id);
        }

        $outcomes = $query->latest('tanggal')->latest('created_at')->paginate(15)->withQueryString();

        $statsQuery = Outcome::query();
        $statsQuery = $this->tenantContextService->scopeByUser($statsQuery, $request->user());

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $statsQuery->where(function ($builder) use ($search) {
                $builder->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $statsQuery->where('kategori', $request->kategori);
        }

        if ($request->filled('outlet_id') && $request->outlet_id !== 'all') {
            $statsQuery->where('outlet_id', $request->outlet_id);
        }

        $totalAmount = (clone $statsQuery)->sum('jumlah');
        $totalEntries = (clone $statsQuery)->count();

        $outlets = $request->user()->isSuperAdmin()
            ? Outlet::query()->orderBy('nama_outlet')->get()
            : Outlet::query()->whereIn('id', $request->user()->accessibleOutletIds())->orderBy('nama_outlet')->get();

        $activeOutletName = 'Semua Cabang';
        if ($request->filled('outlet_id') && $request->outlet_id !== 'all') {
            $activeOutletName = $outlets->firstWhere('id', $request->outlet_id)?->nama_outlet ?? 'Semua Cabang';
        } elseif (! $request->user()->isOwner() && ! $request->user()->isSuperAdmin()) {
            $activeOutletName = $request->user()->outlet?->nama_outlet ?? 'Cabang Aktif';
        }

        return view('pages.outcomes.index', [
            'outcomes' => $outcomes,
            'outlets' => $outlets,
            'categoryOptions' => Outcome::CATEGORY_OPTIONS,
            'stats' => [
                'total_amount' => $totalAmount,
                'total_entries' => $totalEntries,
                'active_outlet_name' => $activeOutletName,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateOutcome($request);
        $this->authorizeOutlet($request, $validated['outlet_id']);

        Outcome::create([
            'outlet_id' => $validated['outlet_id'],
            'user_id' => $request->user()?->id,
            'kategori' => $validated['kategori'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'jumlah' => $validated['jumlah'],
        ]);

        return redirect()->route('outcomes.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function update(Request $request, Outcome $outcome)
    {
        $this->authorizeOutlet($request, $outcome->outlet_id);
        $validated = $this->validateOutcome($request);
        $this->authorizeOutlet($request, $validated['outlet_id']);

        $outcome->update($validated);

        return redirect()->route('outcomes.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Outcome $outcome)
    {
        $this->authorizeOutlet($request, $outcome->outlet_id);
        $outcome->delete();

        return redirect()->route('outcomes.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function validateOutcome(Request $request): array
    {
        return $request->validate([
            'kategori' => 'required|in:' . implode(',', array_keys(Outcome::CATEGORY_OPTIONS)),
            'tanggal' => 'required|date',
            'outlet_id' => 'required|exists:outlets,id',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);
    }

    private function authorizeOutlet(Request $request, string $outletId): void
    {
        abort_if(! in_array($outletId, $request->user()->accessibleOutletIds(), true), 403);
    }
}
