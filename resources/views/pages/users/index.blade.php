@extends('layouts.app')

@section('title', 'User Management - Laundry Track')

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-6 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
    <div class="flex flex-1 items-center gap-4">
        <div class="relative w-full max-w-[500px]">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </div>
            <form action="{{ route('users.index') }}" method="GET">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by name or email..." 
                    class="block w-full rounded-xl border-none bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
            </form>
        </div>
    </div>
    <div class="flex shrink-0 items-center gap-6">
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-extrabold text-slate-900">Admin User</p>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Facility Manager</p>
            </div>
            <div class="h-10 w-10 overflow-hidden rounded-full bg-indigo-100 ring-2 ring-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
                A
            </div>
        </div>
    </div>
</header>
@endsection

@section('content')
<div class="mx-auto max-w-[1400px] space-y-8">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">User Management</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400">Manage system users, roles, and access.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Add User
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-[28px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-3">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Role</label>
                <select name="role" class="rounded-xl border-none bg-slate-50 py-2.5 pl-4 pr-10 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="ADMIN" {{ request('role') == 'ADMIN' ? 'selected' : '' }}>Admin</option>
                    <option value="KASIR" {{ request('role') == 'KASIR' ? 'selected' : '' }}>Kasir</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Status</label>
                <select name="status" class="rounded-xl border-none bg-slate-50 py-2.5 pl-4 pr-10 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            @if(request()->hasAny(['role', 'status', 'search']))
                <a href="{{ route('users.index') }}" class="text-xs font-bold text-rose-500 hover:underline">Clear Filters</a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="overflow-hidden rounded-[32px] bg-white shadow-soft ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/30">
                        <th class="px-8 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">User Info</th>
                        <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Role</th>
                        <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Created At</th>
                        <th class="px-8 py-5 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="group transition hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-extrabold text-slate-500">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $user->nama }}</p>
                                    <p class="text-xs font-medium text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-widest {{ $user->role == 'ADMIN' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $user->aktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                                <span class="text-xs font-bold {{ $user->aktif ? 'text-slate-700' : 'text-slate-400' }}">
                                    {{ $user->aktif ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold text-slate-600">{{ $user->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-primary-50 hover:text-primary-600">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <p class="text-sm font-bold text-slate-400">No users found matching your search.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="border-t border-slate-50 bg-slate-50/30 px-8 py-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
