<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\SubscriptionAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private readonly SubscriptionAccessService $subscriptionAccessService,
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $roles = Role::query()
            ->with('permissions')
            ->withCount('permissions')
            ->when(! $user->isSuperAdmin(), fn ($query) => $query->where('tenant_id', $user->tenant_id))
            ->orderBy('name')
            ->paginate(10);

        return view('pages.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        $user = auth()->user();

        return view('pages.roles.create', [
            'role' => new Role([
                'guard_name' => 'web',
                'tenant_id' => $user->isSuperAdmin() ? null : $user->tenant_id,
            ]),
            'permissions' => $this->availablePermissions(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
        ]);

        $user = $request->user();
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'tenant_id' => $user->isSuperAdmin() ? null : $user->tenant_id,
        ]);

        $role->syncPermissions($this->filterAllowedPermissions($validated['permissions'] ?? []));

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        $this->authorizeRole($role);

        return view('pages.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->availablePermissions(),
            'selectedPermissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
        ]);

        $this->authorizeRole($role);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($this->filterAllowedPermissions($validated['permissions'] ?? []));

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorizeRole($role);

        if ($role->name === User::ROLE_SUPER_ADMIN) {
            return back()->with('error', 'Role Super Admin tidak boleh dihapus.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    private function availablePermissions()
    {
        $user = auth()->user();
        $query = Permission::query()->orderBy('name');

        if ($user->isSuperAdmin()) {
            return $query->get();
        }

        return $query
            ->whereIn('name', $user->activeSubscription()?->plan?->permissions?->pluck('name')->all() ?? [])
            ->get();
    }

    private function filterAllowedPermissions(array $permissions): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $permissions;
        }

        return array_values(array_intersect(
            $permissions,
            $user->activeSubscription()?->plan?->permissions?->pluck('name')->all() ?? [],
        ));
    }

    private function authorizeRole(Role $role): void
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return;
        }

        abort_if($role->tenant_id !== $user->tenant_id, 403);
    }
}
