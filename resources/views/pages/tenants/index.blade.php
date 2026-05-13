@extends('layouts.app')

@section('title', 'Management Tenant')
@section('page-title', 'Management Tenant')
@section('page-subtitle', 'Lihat seluruh tenant laundry, owner, outlet, dan subscription aktifnya.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">Super Admin bisa membuat owner baru secara manual dan mengelola lifecycle subscription tenant.</p>
            <a href="{{ route('tenants.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-soft transition hover:bg-brand-700">Tambah Tenant Owner</a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Tenant</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Owner</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Outlet</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Subscription</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tenants as $tenant)
                            <tr>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $tenant->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $tenant->slug }}</p>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600">
                                    {{ $tenant->owner?->nama ?? '-' }}<br>
                                    <span class="text-slate-400">{{ $tenant->owner?->email ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600">{{ $tenant->outlets->count() }}</td>
                                <td class="px-6 py-5">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ $tenant->activeSubscription?->plan?->name ?? 'No Plan' }} / {{ $tenant->current_subscription_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('tenants.edit', $tenant) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">Kelola</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada tenant yang tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tenants->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $tenants->links() }}</div>
            @endif
        </div>
    </div>
@endsection
