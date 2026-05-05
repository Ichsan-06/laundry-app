<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

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
            $query->where('role', $request->role);
        }

        // Filter by Status
        if ($request->has('status') && $request->status != '') {
            $query->where('aktif', $request->status == 'active');
        }

        $users = $query->orderBy('nama', 'asc')->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:ADMIN,KASIR',
            'password' => 'required|string|min:8|confirmed',
            'aktif' => 'boolean',
        ]);

        User::create([
            'outlet_id' => \App\Models\Outlet::first()->id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password_hash' => Hash::make($validated['password']),
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:ADMIN,KASIR',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->aktif = $request->has('aktif');

        if ($request->filled('password')) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

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
