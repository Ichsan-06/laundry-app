<?php

namespace App\Http\Controllers;

use App\Models\PlanPurchaseHistory;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionAccessService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(
        private readonly SubscriptionAccessService $subscriptionAccessService,
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $tenant = $user->tenant;
        $subscription = $this->subscriptionAccessService->currentSubscription($tenant);
        $plans = SubscriptionPlan::query()
            ->where('name','!=', 'trial')
            ->with('permissions')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (SubscriptionPlan $plan) {
                $plan->menu_summaries = $this->buildMenuSummaries($plan);

                return $plan;
            });
        $purchases = PlanPurchaseHistory::query()
            ->with('plan')
            ->where('tenant_id', $tenant?->id)
            ->latest()
            ->get();

        return view('pages.billing.index', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'status' => $this->subscriptionAccessService->statusLabel($subscription),
            'plans' => $plans,
            'pendingPurchases' => $purchases->where('status', 'pending')->values(),
            'purchaseHistories' => $purchases,
        ]);
    }

    private function buildMenuSummaries(SubscriptionPlan $plan): Collection
    {
        $definitions = [
            'dashboard' => ['menu' => 'Dashboard', 'description' => 'Melihat ringkasan usaha dan aktivitas laundry.'],
            'cashier' => ['menu' => 'Kasir', 'description' => 'Melayani transaksi langsung dari meja kasir.'],
            'transactions' => ['menu' => 'Transaksi', 'description' => 'Mengelola pesanan laundry dari input sampai perubahan data.'],
            'customers' => ['menu' => 'Pelanggan', 'description' => 'Menyimpan dan memperbarui data pelanggan laundry.'],
            'machines' => ['menu' => 'Mesin', 'description' => 'Mengatur daftar mesin yang dipakai operasional.'],
            'services' => ['menu' => 'Layanan', 'description' => 'Mengelola jenis layanan dan paket cucian.'],
            'addons' => ['menu' => 'Add-on', 'description' => 'Mengatur layanan tambahan di luar paket utama.'],
            'reports' => ['menu' => 'Laporan', 'description' => 'Melihat performa usaha dan mengekspor laporan.'],
            'outlets' => ['menu' => 'Outlet', 'description' => 'Mengatur data outlet dan cabang laundry.'],
            'staff' => ['menu' => 'Staff', 'description' => 'Menambah dan mengatur akun staff operasional.'],
            'roles' => ['menu' => 'Role Akses', 'description' => 'Mengatur peran dan pembagian akses staff.'],
            'permissions' => ['menu' => 'Permission', 'description' => 'Mengatur detail izin akses secara lebih rinci.'],
            'settings' => ['menu' => 'Pengaturan', 'description' => 'Mengubah pengaturan utama bisnis laundry.'],
            'billing' => ['menu' => 'Billing', 'description' => 'Melihat paket langganan dan status pembayaran.'],
            'promo' => ['menu' => 'Promo', 'description' => 'Mengatur promo dan penawaran untuk pelanggan.'],
            'plans' => ['menu' => 'Paket Langganan', 'description' => 'Mengelola pilihan paket langganan sistem.'],
            'tenants' => ['menu' => 'Tenant', 'description' => 'Mengelola data tenant atau cabang usaha yang terdaftar.'],
            'subscription' => ['menu' => 'Langganan', 'description' => 'Mengatur aktivasi dan perubahan masa langganan tenant.'],
            'users' => ['menu' => 'Pengguna', 'description' => 'Mengelola akun pengguna di dalam tenant.'],
        ];

        return $plan->permissions
            ->pluck('name')
            ->map(fn (string $permission) => explode('.', $permission)[0])
            ->unique()
            ->values()
            ->map(function (string $group) use ($definitions) {
                return $definitions[$group] ?? [
                    'menu' => str($group)->replace('_', ' ')->title()->toString(),
                    'description' => 'Akses untuk mengelola menu ini tersedia di paket ini.',
                ];
            });
    }
}
