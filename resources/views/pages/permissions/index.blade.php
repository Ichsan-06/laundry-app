@extends('layouts.app')

@section('title', 'Management Permission')
@section('page-title', 'Management Permission')
@section('page-subtitle', 'Kelola daftar permission dan lihat role mana saja yang memilikinya.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">Permission dipakai untuk mengontrol halaman, menu, dan aksi yang boleh diakses user.</p>
            <a href="{{ route('permissions.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-soft transition hover:bg-brand-700">
                Tambah Permission
            </a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Permission</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Guard</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Role Yang Memiliki</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($permissions as $permission)
                            <tr>
                                <td class="px-6 py-5 text-sm font-extrabold text-slate-900">{{ $permission->name }}</td>
                                <td class="px-6 py-5 text-sm text-slate-500">{{ $permission->guard_name }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($permission->roles as $role)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-sm text-slate-400">Belum dipakai role mana pun.</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('permissions.edit', $permission) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Hapus permission ini?')">
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
                                <td colspan="4" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada permission yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($permissions->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
