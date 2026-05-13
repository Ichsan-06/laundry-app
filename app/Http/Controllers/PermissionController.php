<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::with('roles')
            ->orderBy('name')
            ->paginate(12);

        return view('pages.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        return view('pages.permissions.create', [
            'permission' => new Permission(['guard_name' => 'web']),
            'roles' => Role::orderBy('name')->get(),
            'selectedRoles' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['roles'])) {
            $roles = Role::whereIn('name', $validated['roles'])->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission): View
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        return view('pages.permissions.edit', [
            'permission' => $permission->load('roles'),
            'roles' => Role::orderBy('name')->get(),
            'selectedRoles' => $permission->roles->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ], [
            'name.required' => 'Nama permission wajib diisi.',
            'name.unique' => 'Nama permission sudah digunakan.',
        ]);

        $permission->update([
            'name' => $validated['name'],
        ]);

        $permission->roles()->detach();

        if (! empty($validated['roles'])) {
            $roles = Role::whereIn('name', $validated['roles'])->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
