@extends('layouts.app')

@section('title', 'Management User')
@section('page-title', 'Management User')
@section('page-subtitle', 'Kelola akun operator, status aktif, dan role akses masing-masing pengguna.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-[32px] border border-white/70 bg-white p-6 shadow-soft lg:flex-row lg:items-end lg:justify-between">
            <form action="{{ route('users.index') }}" method="GET" class="grid flex-1 gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-[0.24em] text-slate-400">Cari User</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-[0.24em] text-slate-400">Filter Role</label>
                    <select name="role" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-[0.24em] text-slate-400">Status</label>
                    <select name="status" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>

            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-ink px-5 py-3 text-sm font-extrabold text-white transition hover:bg-slate-900">
                Tambah User
            </a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">User</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Role</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Dibuat</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-600">
                                            {{ strtoupper(str($user->nama)->substr(0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900">{{ $user->nama }}</p>
                                            <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">{{ $user->display_role }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="{{ $user->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} rounded-full px-3 py-1 text-xs font-bold">
                                        {{ $user->aktif ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-2xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 transition hover:bg-rose-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada user yang cocok dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
