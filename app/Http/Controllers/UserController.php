<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use App\Services\SubscriptionAccessService;
use App\Services\TenantContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private readonly TenantContextService $tenantContextService,
        private readonly SubscriptionAccessService $subscriptionAccessService,
    ) {
    }

    public function index(Request $request)
    {
        $currentUser = $request->user();
        $query = User::query()
            ->with(['roles', 'outlet', 'tenant'])
            ->when(! $currentUser->isSuperAdmin(), function ($builder) use ($currentUser) {
                $builder->where('tenant_id', $currentUser->tenant_id);
            });

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by Role
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function ($roleQuery) use ($request) {
                $roleQuery->where('name', $request->role);
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('aktif', $request->status == 'active');
        }

        $users = $query->orderBy('nama', 'asc')->paginate(10);

        return view('pages.users.index', [
            'users' => $users,
            'roles' => $this->availableRoles($currentUser)->pluck('name')->all(),
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        return view('pages.users.create', [
            'roles' => $this->availableRoles($user)->pluck('name')->all(),
            'outlets' => $this->availableOutlets($user),
        ]);
    }

    public function store(Request $request)
    {
        $currentUser = $request->user();
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8|confirmed',
            'outlet_id' => 'nullable|exists:outlets,id',
            'aktif' => 'boolean',
        ]);

        if (! $currentUser->isSuperAdmin() && ! $this->subscriptionAccessService->canCreateStaff($currentUser->tenant)) {
            return back()->withErrors([
                'role' => 'Batas jumlah staff untuk paket langganan Anda sudah tercapai.',
            ])->withInput();
        }

        $role = $this->availableRoles($currentUser)->firstWhere('name', $validated['role']);
        abort_unless($role, 403);

        $outletId = $validated['outlet_id'] ?? null;
        if ($currentUser->isOwner() && in_array($validated['role'], [User::ROLE_KASIR, User::ROLE_MANAGER, User::ROLE_OPERATOR], true)) {
            if (! $outletId) {
                return back()->withErrors([
                    'outlet_id' => 'Staff wajib ditugaskan ke salah satu outlet.',
                ])->withInput();
            }
        }

        $user = User::create([
            'outlet_id' => $outletId,
            'tenant_id' => $currentUser->isSuperAdmin() ? null : $currentUser->tenant_id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => User::legacyRoleFromRoleName($validated['role']),
            'password_hash' => Hash::make($validated['password']),
            'user_type' => $validated['role'] === User::ROLE_OWNER ? 'owner' : ($validated['role'] === User::ROLE_SUPER_ADMIN ? 'super_admin' : 'staff'),
            'aktif' => $request->has('aktif'),
        ]);

        $user->assignRole($validated['role']);
        $user->syncLegacyRoleColumn();

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorizeUserRecord($user);

        return view('pages.users.edit', [
            'user' => $user->load('roles'),
            'roles' => $this->availableRoles(auth()->user())->pluck('name')->all(),
            'outlets' => $this->availableOutlets(auth()->user()),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUserRecord($user);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
            'outlet_id' => 'nullable|exists:outlets,id',
        ]);

        $role = $this->availableRoles($request->user())->firstWhere('name', $validated['role']);
        abort_unless($role, 403);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->role = User::legacyRoleFromRoleName($validated['role']);
        $user->tenant_id = $request->user()->isSuperAdmin() ? $user->tenant_id : $request->user()->tenant_id;
        $user->outlet_id = $validated['outlet_id'] ?? null;
        $user->user_type = $validated['role'] === User::ROLE_OWNER ? 'owner' : ($validated['role'] === User::ROLE_SUPER_ADMIN ? 'super_admin' : 'staff');
        $user->aktif = $request->has('aktif');

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);
        $user->syncLegacyRoleColumn();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeUserRecord($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function availableRoles(User $currentUser)
    {
        if ($currentUser->isSuperAdmin()) {
            return Role::query()
                ->whereNull('tenant_id')
                ->orderBy('name')
                ->get();
        }

        $allowedPermissions = $currentUser->activeSubscription()?->plan?->permissions?->pluck('name')->all() ?? [];

        return Role::query()
            ->where(function ($builder) use ($currentUser) {
                $builder->whereNull('tenant_id')
                    ->orWhere('tenant_id', $currentUser->tenant_id);
            })
            ->where('name', '!=', User::ROLE_SUPER_ADMIN)
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->filter(function (Role $role) use ($allowedPermissions) {
                if ($role->name === User::ROLE_OWNER) {
                    return false;
                }

                $rolePermissions = $role->permissions->pluck('name')->all();

                return empty(array_diff($rolePermissions, $allowedPermissions));
            })
            ->values();
    }

    private function availableOutlets(User $currentUser)
    {
        if ($currentUser->isSuperAdmin()) {
            return Outlet::query()->orderBy('nama_outlet')->get();
        }

        return Outlet::query()
            ->where('tenant_id', $currentUser->tenant_id)
            ->orderBy('nama_outlet')
            ->get();
    }

    private function authorizeUserRecord(User $user): void
    {
        $currentUser = auth()->user();

        if ($currentUser->isSuperAdmin()) {
            return;
        }

        abort_if($user->tenant_id !== $currentUser->tenant_id, 403);
    }
}
