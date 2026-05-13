@extends('layouts.app')

@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')
@section('page-subtitle', 'Super Admin dapat mengatur limit paket dan permission yang aktif di setiap plan.')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">Permission plan akan menjadi lapisan akses tambahan di atas role permission user.</p>
            <a href="{{ route('subscription-plans.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-soft transition hover:bg-brand-700">Tambah Plan</a>
        </div>

        <div class="overflow-hidden rounded-[32px] border border-white/70 bg-white shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Plan</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Limit</th>
                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Permission</th>
                            <th class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-[0.24em] text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($plans as $plan)
                            <tr class="align-top">
                                <td class="px-6 py-5">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $plan->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $plan->slug }}</p>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600">
                                    Outlet: {{ $plan->max_outlets ?? 'Unlimited' }}<br>
                                    Staff: {{ $plan->max_staff ?? 'Unlimited' }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($plan->permissions->take(6) as $permission)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $permission->name }}</span>
                                        @endforeach
                                        @if ($plan->permissions_count > 6)
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">+{{ $plan->permissions_count - 6 }} lainnya</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('subscription-plans.edit', $plan) }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">Edit</a>
                                        <form action="{{ route('subscription-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Hapus plan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-2xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 transition hover:bg-rose-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-14 text-center text-sm font-semibold text-slate-400">Belum ada subscription plan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($plans->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $plans->links() }}</div>
            @endif
        </div>
    </div>
@endsection
