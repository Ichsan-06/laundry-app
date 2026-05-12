<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

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
            'roles' => Role::orderBy('name')->pluck('name')->all(),
        ]);
    }

    public function create()
    {
        return view('pages.users.create', [
            'roles' => Role::orderBy('name')->pluck('name')->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8|confirmed',
            'aktif' => 'boolean',
        ]);

        $outlet = Outlet::firstOrCreate([
            'nama_outlet' => 'Laundry Express Utama',
        ], [
            'alamat' => 'Jl. Merdeka No. 123',
            'telepon' => '021-1234567',
            'kota' => 'Jakarta',
            'aktif' => true,
        ]);

        $user = User::create([
            'outlet_id' => $outlet?->id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => User::legacyRoleFromRoleName($validated['role']),
            'password_hash' => Hash::make($validated['password']),
            'aktif' => $request->has('aktif'),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->role = User::legacyRoleFromRoleName($validated['role']);
        $user->aktif = $request->has('aktif');

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
