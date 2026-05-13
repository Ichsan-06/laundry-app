@extends('layouts.app')

@section('title', 'Management Outlet')
@section('page-title', 'Management Outlet')
@section('page-subtitle', 'Kelola outlet tenant sesuai limit paket langganan yang sedang aktif.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm font-semibold text-slate-500">Owner dapat mengelola outlet miliknya, sedangkan staff tetap dibatasi pada outlet penugasannya.</div>
            <a href="{{ route('outlets.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-soft transition hover:bg-brand-700">Tambah Outlet</a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Outlet</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Kota</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($outlets as $outlet)
                            <tr>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $outlet->nama_outlet }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $outlet->alamat }}</p>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600">{{ $outlet->kota }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ $outlet->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} rounded-full px-3 py-1 text-xs font-bold">{{ $outlet->aktif ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('outlets.edit', $outlet) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">Edit</a>
                                        <form action="{{ route('outlets.destroy', $outlet) }}" method="POST" onsubmit="return confirm('Hapus outlet ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-2xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada outlet yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($outlets->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $outlets->links() }}</div>
            @endif
        </div>
    </div>
@endsection
