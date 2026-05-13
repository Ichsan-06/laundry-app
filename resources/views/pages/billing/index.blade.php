@extends('layouts.app')

@section('title', 'Billing & Subscription')
@section('page-title', 'Billing & Subscription')
@section('page-subtitle', 'Pantau paket aktif, masa berlaku, dan fitur yang tersedia untuk tenant Anda.')

@section('content')
    <div
        x-data="{
            csrfToken: @js(csrf_token()),
            purchaseUrl: @js(route('billing.purchase')),
            purchaseStatusTemplate: @js(route('billing.purchase.status', ['purchase' => '__PURCHASE__'])),
            qrisModal: { show: false, purchaseId: null, planId: null, planName: '', refId: '', trxReference: '', paymentName: 'QRIS', qrImage: '', amount: 0, paymentFee: 0, expired: null, remainingSeconds: 0, status: 'pending', tutorialPembayaran: '' },
            pollingTimer: null,
            countdownTimer: null,
            statusUrl(id) { return this.purchaseStatusTemplate.replace('__PURCHASE__', id); },
            formatCurrency(value) { return 'Rp ' + Number(value || 0).toLocaleString('id-ID'); },
            resetTimers() {
                if (this.pollingTimer) clearInterval(this.pollingTimer);
                if (this.countdownTimer) clearInterval(this.countdownTimer);
                this.pollingTimer = null;
                this.countdownTimer = null;
            },
            openPaymentModal(payload) {
                this.resetTimers();
                const expiredAt = payload.expired ? new Date(payload.expired) : null;
                this.qrisModal = {
                    show: true,
                    purchaseId: payload.purchase_id,
                    planId: payload.plan_id,
                    planName: payload.plan_name,
                    refId: payload.ref_id,
                    trxReference: payload.trx_reference,
                    paymentName: payload.payment_name || 'QRIS',
                    qrImage: payload.qr_image,
                    amount: Number(payload.amount || 0),
                    paymentFee: Number(payload.payment_fee || 0),
                    expired: expiredAt,
                    remainingSeconds: expiredAt ? Math.max(0, Math.floor((expiredAt.getTime() - Date.now()) / 1000)) : 0,
                    status: payload.payment_status || 'pending',
                    tutorialPembayaran: payload.tutorial_pembayaran || '',
                };
                this.startCountdown();
                this.startPolling();
            },
            closePaymentModal() { this.resetTimers(); this.qrisModal.show = false; },
            startCountdown() {
                this.countdownTimer = setInterval(() => {
                    if (!this.qrisModal.expired) return;
                    const remaining = Math.max(0, Math.floor((this.qrisModal.expired.getTime() - Date.now()) / 1000));
                    this.qrisModal.remainingSeconds = remaining;
                    if (remaining <= 0) {
                        this.qrisModal.status = 'expired';
                        this.resetTimers();
                    }
                }, 1000);
            },
            startPolling() {
                if (!this.qrisModal.purchaseId) return;
                this.pollingTimer = setInterval(() => this.checkPurchaseStatus(false), 5000);
            },
            formatCountdown(seconds) {
                const total = Math.max(0, Number(seconds || 0));
                return String(Math.floor(total / 60)).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
            },
            async buyPlan(planId, purchaseId = null) {
                const formData = new FormData();
                formData.set('subscription_plan_id', planId);
                if (purchaseId) formData.set('purchase_id', purchaseId);
                const response = await fetch(this.purchaseUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Pembayaran plan gagal dibuat.');
                this.openPaymentModal(result);
            },
            async checkPurchaseStatus(reloadOnPaid = true) {
                if (!this.qrisModal.purchaseId || this.qrisModal.status === 'paid') return;
                const response = await fetch(this.statusUrl(this.qrisModal.purchaseId), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }});
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Status pembayaran plan gagal diperiksa.');
                this.qrisModal.status = result.payment_status;
                this.qrisModal.trxReference = result.trx_reference || this.qrisModal.trxReference;
                if (result.expired) this.qrisModal.expired = new Date(result.expired);
                if (result.payment_status === 'paid') {
                    this.resetTimers();
                    if (reloadOnPaid) window.location.reload();
                }
            }
        }"
        class="space-y-6"
    >
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
                                <p class="mt-3 text-2xl font-extrabold text-slate-900">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}<span class="text-sm font-semibold text-slate-400">/bulan</span></p>
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
                        <div class="mt-5">
                            <button
                                type="button"
                                @click="buyPlan(@js($plan->id))"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-600 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700"
                            >
                                Beli / Upgrade ke {{ $plan->name }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Pending Payment</p>
                <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Pembayaran Upgrade yang Menunggu</h3>

                <div class="mt-6 space-y-4">
                    @forelse ($pendingPurchases as $purchase)
                        <div class="rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-extrabold text-slate-900">{{ $purchase->plan_name_snapshot }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Ref: {{ $purchase->ref_id }}</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-700">Total: Rp {{ number_format($purchase->amount, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Expire: {{ optional($purchase->payment_expires_at)->format('d M Y H:i') ?? '-' }}</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-amber-700">{{ $purchase->status }}</span>
                                    <button type="button" @click="buyPlan(@js($purchase->subscription_plan_id), @js($purchase->id))" class="rounded-2xl bg-brand-600 px-4 py-2 text-sm font-extrabold text-white">Bayar Lagi</button>
                                    <button
                                        type="button"
                                        @click="openPaymentModal({
                                            purchase_id: @js($purchase->id),
                                            plan_id: @js($purchase->subscription_plan_id),
                                            plan_name: @js($purchase->plan_name_snapshot),
                                            ref_id: @js($purchase->ref_id),
                                            trx_reference: @js($purchase->trx_reference),
                                            payment_name: @js($purchase->payment_name),
                                            amount: @js($purchase->amount),
                                            payment_fee: @js($purchase->payment_fee),
                                            expired: @js(optional($purchase->payment_expires_at)->toIso8601String()),
                                            qr_image: @js($purchase->qr_image),
                                            tutorial_pembayaran: @js($purchase->tutorial_pembayaran),
                                            payment_status: @js($purchase->status),
                                        })"
                                        class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700"
                                    >Lihat QR</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-medium text-slate-500">
                            Belum ada pembayaran upgrade plan yang pending.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[32px] border border-white/70 bg-white p-6 shadow-soft">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Purchase History</p>
                <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Riwayat Pembelian Plan</h3>

                <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Nominal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($purchaseHistories as $history)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-bold text-slate-800">{{ $history->plan_name_snapshot }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">Rp {{ number_format($history->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] {{ $history->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($history->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $history->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-500">{{ optional($history->created_at)->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm font-medium text-slate-500">Belum ada riwayat pembelian plan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="qrisModal.show" class="fixed inset-0 z-[85] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" x-cloak>
            <div class="w-full max-w-2xl rounded-[32px] bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.26em] text-primary-600">Pembayaran Upgrade Plan</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900" x-text="qrisModal.planName"></h3>
                        <p class="mt-1 text-sm font-medium text-slate-500" x-text="'Ref: ' + qrisModal.refId"></p>
                    </div>
                    <button type="button" @click="closePaymentModal()" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600">Tutup</button>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <img :src="qrisModal.qrImage" alt="QRIS Code" class="mx-auto h-64 w-64 rounded-2xl border border-slate-100 bg-white object-contain p-3">
                    </div>
                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Nominal</p>
                                <p class="mt-2 text-xl font-extrabold text-slate-900" x-text="formatCurrency(qrisModal.amount)"></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Fee</p>
                                <p class="mt-2 text-xl font-extrabold text-slate-900" x-text="formatCurrency(qrisModal.paymentFee)"></p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Status Pembayaran</p>
                            <div class="mt-2 flex items-center justify-between gap-4">
                                <p class="text-lg font-extrabold" :class="qrisModal.status === 'paid' ? 'text-emerald-600' : (qrisModal.status === 'expired' ? 'text-rose-600' : 'text-amber-600')" x-text="qrisModal.status.toUpperCase()"></p>
                                <p class="text-lg font-extrabold text-primary-600" x-show="qrisModal.status !== 'paid'" x-text="formatCountdown(qrisModal.remainingSeconds)"></p>
                            </div>
                        </div>

                        <div x-show="qrisModal.tutorialPembayaran" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Tutorial</p>
                            <p class="mt-2 whitespace-pre-line text-sm font-medium leading-6 text-slate-600" x-text="qrisModal.tutorialPembayaran"></p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" @click="checkPurchaseStatus()" class="rounded-2xl bg-brand-600 px-4 py-3 text-sm font-extrabold text-white">Cek Status</button>
                            <button type="button" @click="buyPlan(qrisModal.planId, qrisModal.purchaseId)" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700">Generate Ulang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
