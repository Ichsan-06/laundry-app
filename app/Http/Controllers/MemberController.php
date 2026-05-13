<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Outlet;
use App\Services\TenantContextService;

class MemberController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
    ) {
    }

    public function index(Request $request)
    {
        $query = Member::query()->with('outlet');
        $query = $this->tenantContextService->scopeByUser($query, $request->user());

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('id_member', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'nama') {
            $query->orderBy('nama', 'asc');
        } elseif ($sort === 'saldo_high') {
            $query->orderBy('saldo', 'desc');
        } else {
            $query->latest();
        }

        $members = $query->paginate(10)->withQueryString();
        $statsQuery = Member::query();
        $statsQuery = $this->tenantContextService->scopeByUser($statsQuery, $request->user());

        // Stats
        $stats = [
            'total_members' => (clone $statsQuery)->count(),
            'total_balance' => (clone $statsQuery)->sum('saldo'),
            'active_passports' => (clone $statsQuery)->where('status', 'PREMIUM')->count(),
            'low_balance_alerts' => (clone $statsQuery)->where('status', 'LOW_BALANCE')->count(),
        ];

        return view('pages.members.index', compact('members', 'stats'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Member::class);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,LOW_BALANCE,INACTIVE,PREMIUM',
        ]);

        $outlet = $request->user()->isOwner()
            ? Outlet::query()->where('tenant_id', $request->user()->tenant_id)->orderBy('nama_outlet')->first()
            : $request->user()->outlet;

        $validated['outlet_id'] = $outlet->id;
        $validated['id_member'] = 'MEM-' . strtoupper(bin2hex(random_bytes(3)));
        $validated['tanggal_daftar'] = now();

        Member::create($validated);

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    public function update(Request $request, Member $member)
    {
        $this->authorize('update', $member);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,LOW_BALANCE,INACTIVE,PREMIUM',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->authorize('delete', $member);

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
