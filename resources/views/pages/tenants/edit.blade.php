@extends('layouts.app')

@section('title', 'Kelola Tenant')
@section('page-title', 'Kelola Tenant')
@section('page-subtitle', 'Perbarui status tenant dan assign subscription plan aktifnya.')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Tenant Profile</p>
                <form action="{{ route('tenants.update', $tenant) }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Nama Tenant</label>
                        <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Status</label>
                        <select name="status" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $tenant->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">Update Tenant</button>
                </form>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Subscription Assignment</p>
                <form action="{{ route('tenants.subscription.update', $tenant) }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-slate-700">Pilih Plan</label>
                            <select name="subscription_plan_id" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('subscription_plan_id', $tenant->activeSubscription?->subscription_plan_id) === $plan->id)>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Status Subscription</label>
                            <select name="status" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                                @foreach (['trial' => 'Trial', 'active' => 'Active', 'expired' => 'Expired', 'inactive' => 'Inactive'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $tenant->activeSubscription?->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Mulai</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($tenant->activeSubscription?->starts_at)->format('Y-m-d\\TH:i')) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Akhir Trial</label>
                            <input type="datetime-local" name="trial_ends_at" value="{{ old('trial_ends_at', optional($tenant->activeSubscription?->trial_ends_at)->format('Y-m-d\\TH:i')) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Akhir Langganan</label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($tenant->activeSubscription?->ends_at)->format('Y-m-d\\TH:i')) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <input type="checkbox" name="is_trial" value="1" @checked(old('is_trial', $tenant->activeSubscription?->is_trial)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-bold text-slate-700">Tandai sebagai trial</span>
                        </label>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <input type="checkbox" name="grace_dashboard_only" value="1" @checked(old('grace_dashboard_only', $tenant->activeSubscription?->grace_dashboard_only ?? true)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-bold text-slate-700">Expired hanya dashboard-only</span>
                        </label>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-ink px-5 py-3 text-sm font-extrabold text-white transition hover:bg-slate-900">Simpan Subscription</button>
                </form>
            </div>
        </div>
    </div>
@endsection
