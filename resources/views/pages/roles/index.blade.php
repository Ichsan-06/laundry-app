@extends('layouts.app')

@section('title', 'Management Role')
@section('page-title', 'Management Role')
@section('page-subtitle', 'Kelola role dan jumlah permission yang terhubung ke masing-masing role.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-500">Role menentukan area dan aksi yang dapat diakses user di aplikasi.</p>
            </div>
            <a href="{{ route('roles.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-soft transition hover:bg-brand-700">
                Tambah Role
            </a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Role</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Permission</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Daftar Permission</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($roles as $role)
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $role->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Guard: {{ $role->guard_name }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">
                                        {{ $role->permissions_count }} permission
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($role->permissions as $permission)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-sm text-slate-400">Belum ada permission.</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('roles.edit', $role) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Hapus role ini?')">
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
                                <td colspan="4" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada role yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($roles->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
