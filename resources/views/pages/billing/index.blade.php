@extends('layouts.app')

@section('title', 'Billing & Subscription')
@section('page-title', 'Billing & Subscription')
@section('page-subtitle', 'Pantau paket aktif, masa berlaku, dan fitur yang tersedia untuk tenant Anda.')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Subscription Status</p>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <h2 class="text-3xl font-extrabold text-slate-900">{{ $subscription?->plan?->name ?? 'Belum ada plan' }}</h2>
                    <span class="{{ $status === 'expired' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.22em]">{{ $status }}</span>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Mulai</p>
                        <p class="mt-2 text-lg font-extrabold text-slate-900">{{ optional($subscription?->starts_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Akhir Trial</p>
                        <p class="mt-2 text-lg font-extrabold text-slate-900">{{ optional($subscription?->trial_ends_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Akhir Langganan</p>
                        <p class="mt-2 text-lg font-extrabold text-slate-900">{{ optional($subscription?->ends_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-medium text-slate-600">
                    @if ($status === 'expired')
                        Langganan tenant Anda sedang expired. Dashboard masih bisa diakses, tetapi transaksi dan fitur premium dikunci sampai plan diperpanjang oleh Super Admin.
                    @else
                        Tenant Anda sedang aktif. Permission efektif akan mengikuti kombinasi role staff dan permission plan yang sedang berjalan.
                    @endif
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Tenant</p>
                <h3 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $tenant?->name ?? '-' }}</h3>
                <div class="mt-5 space-y-3 text-sm text-slate-500">
                    <p><span class="font-bold text-slate-700">Owner:</span> {{ $tenant?->owner?->nama ?? '-' }}</p>
                    <p><span class="font-bold text-slate-700">Outlet:</span> {{ $tenant?->outlets()->count() ?? 0 }}</p>
                    <p><span class="font-bold text-slate-700">Staff:</span> {{ $tenant?->users()->where('user_type', 'staff')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Plan Catalog</p>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Paket yang Tersedia</h3>
                </div>
            </div>

            <div class="mt-6 grid gap-4 xl:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-extrabold text-slate-900">{{ $plan->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $plan->description }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-500">{{ $plan->slug }}</span>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-600">
                                Max Outlet: <span class="font-extrabold text-slate-900">{{ $plan->max_outlets ?? 'Unlimited' }}</span>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-600">
                                Max Staff: <span class="font-extrabold text-slate-900">{{ $plan->max_staff ?? 'Unlimited' }}</span>
                            </div>
                        </div>
                        <div class="mt-5 rounded-3xl bg-white p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Menu yang Didapat</p>
                            <div class="mt-4 space-y-3">
                                @foreach ($plan->menu_summaries as $menu)
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                                        <p class="text-sm font-extrabold text-slate-900">{{ $menu['menu'] }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $menu['description'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
