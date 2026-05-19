@php
    $currentUser = auth()->user();
    $currentTenant = $currentUser?->tenant;
    $currentSubscription = $currentTenant?->activeSubscription;
    $subscriptionStatus = $currentUser
        ? app(\App\Services\SubscriptionAccessService::class)->statusLabel($currentSubscription)
        : 'inactive';

    $mainMenus = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard*', 'permission' => 'dashboard.view'],
        ['label' => 'Kasir', 'route' => 'kasir.index', 'match' => 'kasir*', 'permission' => 'cashier.access'],
        ['label' => 'Pelanggan', 'route' => 'members.index', 'match' => 'members*', 'permission' => 'customers.view'],
        ['label' => 'Transaksi', 'route' => 'transactions.index', 'match' => 'transactions*', 'permission' => 'transactions.view'],
        ['label' => 'Kalender', 'route' => 'calendar.index', 'match' => 'calendar*', 'permission' => 'calendar.view'],
        ['label' => 'Mesin', 'route' => 'machines.index', 'match' => 'machines*', 'permission' => 'machines.view'],
        ['label' => 'Layanan', 'route' => 'services.index', 'match' => 'services*', 'permission' => 'services.view'],
        ['label' => 'Addon', 'route' => 'addons.index', 'match' => 'addons*', 'permission' => 'addons.view'],
        ['label' => 'Inventaris', 'route' => 'inventories.index', 'match' => 'inventories*', 'permission' => 'inventories.view'],
        ['label' => 'Pengeluaran', 'route' => 'outcomes.index', 'match' => 'outcomes*', 'permission' => 'outcomes.view'],
        ['label' => 'Outlet', 'route' => 'outlets.index', 'match' => 'outlets*', 'permission' => 'outlets.view'],
        ['label' => 'Laporan', 'route' => 'reports.index', 'match' => 'reports*', 'permission' => 'reports.view'],
        ['label' => 'Staff', 'route' => 'users.index', 'match' => 'users*', 'permission' => 'staff.view'],
        ['label' => 'Langganan', 'route' => 'billing.index', 'match' => 'billing*', 'permission' => 'billing.view'],
    ];

    $systemMenus = [
        ['label' => 'Roles', 'route' => 'roles.index', 'match' => 'roles*', 'permission' => 'roles.view'],
        ['label' => 'Permissions', 'route' => 'permissions.index', 'match' => 'permissions*', 'permission' => 'permissions.view'],
        ['label' => 'Plans', 'route' => 'subscription-plans.index', 'match' => 'subscription-plans*', 'permission' => 'plans.manage'],
        ['label' => 'Tenants', 'route' => 'tenants.index', 'match' => 'tenants*', 'permission' => 'tenants.manage'],
        ['label' => 'Pengaturan', 'route' => 'settings.index', 'match' => 'settings*', 'permission' => 'settings.manage'],
    ];

    $outletName = $currentUser?->isOwner()
        ? ($currentTenant?->name ?? 'Laundry Control')
        : ($currentUser?->outlet?->nama_outlet ?? $currentTenant?->name ?? 'Laundry Control');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laundry Control')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#10243f',
                        primary: {
                            50: '#eef8ff',
                            100: '#d9efff',
                            300: '#7fd4f6',
                            500: '#0f9bd7',
                            600: '#0a84bc',
                            700: '#096892',
                        },
                        brand: {
                            50: '#eef8ff',
                            100: '#d9efff',
                            500: '#0f9bd7',
                            600: '#0a84bc',
                            700: '#096892',
                        },
                        accent: {
                            100: '#fff0cf',
                            500: '#f4a100',
                            600: '#d68900',
                        },
                    },
                    boxShadow: {
                        panel: '0 20px 60px rgba(15, 35, 63, 0.08)',
                        soft: '0 12px 30px rgba(15, 35, 63, 0.06)',
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,155,215,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(244,161,0,0.16),_transparent_28%),linear-gradient(180deg,_#f8fbff_0%,_#f4f7fb_100%)]">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/30 lg:hidden" @click="sidebarOpen = false"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex w-80 flex-col border-r border-white/70 bg-white/90 p-6 shadow-panel backdrop-blur transition-transform duration-300 lg:translate-x-0">
            <div class="flex items-center gap-4">
                <img src="{{ asset('logo.png') }}" alt="WashKita Logo" class="h-14 w-14 rounded-2xl object-cover shadow-soft">
                <div>
                    <p class="text-lg font-extrabold text-ink">{{ $outletName }}</p>
                    <p class="text-sm font-semibold text-slate-400">Laundry SaaS Panel</p>
                </div>
            </div>

            <div class="mt-8 rounded-3xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Signed In</p>
                <p class="mt-2 text-base font-extrabold text-ink">{{ $currentUser?->nama }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">{{ $currentUser?->display_role }}</span>
                    @role('Super Admin')
                        <span class="rounded-full bg-accent-100 px-3 py-1 text-xs font-bold text-amber-700">Root Access</span>
                    @endrole
                </div>
                @if ($currentTenant)
                    <div class="mt-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-500">
                        <p class="font-bold text-slate-700">Tenant: {{ $currentTenant->name }}</p>
                        <p class="mt-1 uppercase tracking-[0.18em] text-slate-400">Subscription {{ $subscriptionStatus }}</p>
                        <!-- Upgrade Plan -->
                            <a href="{{ route('billing.index') }}" class="mt-3 inline-block rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-bold text-amber-700 transition hover:bg-amber-100">
                                Upgrade Plan
                            </a>
                    </div>
                @endif
            </div>

            <nav class="mt-8 flex-1 space-y-2 overflow-y-auto pr-1">
                <p class="px-3 text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Main Menu</p>
                @foreach ($mainMenus as $menu)
                    @can($menu['permission'])
                        <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs($menu['match']) ? 'bg-ink text-white shadow-soft' : 'text-slate-500 hover:bg-slate-100 hover:text-ink' }} flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-bold transition">
                            <span>{{ $menu['label'] }}</span>
                            <span class="{{ request()->routeIs($menu['match']) ? 'bg-white/15 text-white' : 'bg-white text-slate-400' }} rounded-full px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.2em]">
                                {{ str($menu['label'])->substr(0, 1) }}
                            </span>
                        </a>
                    @endcan
                @endforeach

                @canany(['roles.view', 'permissions.view', 'settings.manage', 'plans.manage', 'tenants.manage'])
                    <div class="pt-6">
                        <p class="px-3 text-xs font-bold uppercase tracking-[0.28em] text-slate-400">System</p>
                        <div class="mt-2 space-y-2">
                            @foreach ($systemMenus as $menu)
                                @can($menu['permission'])
                                    <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs($menu['match']) ? 'bg-brand-600 text-white shadow-soft' : 'text-slate-500 hover:bg-slate-100 hover:text-ink' }} flex items-center rounded-2xl px-4 py-3 text-sm font-bold transition">
                                        {{ $menu['label'] }}
                                    </a>
                                @endcan
                            @endforeach
                        </div>
                    </div>
                @endcanany
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="pt-4">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                    Logout
                </button>
            </form>
        </aside>

        <div class="lg:pl-80">
            <header class="sticky top-0 z-30 border-b border-white/60 bg-white/80 backdrop-blur">
                @hasSection('header')
                    @yield('header')
                @else
                    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-10">
                        <div class="flex items-center gap-3">
                            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 lg:hidden" @click="sidebarOpen = true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            <div>
                                <p class="text-sm font-bold uppercase tracking-[0.26em] text-slate-400">@yield('section-label', 'Laundry App')</p>
                                <h1 class="text-2xl font-extrabold tracking-tight text-ink">@yield('page-title', 'Dashboard')</h1>
                                <p class="text-sm font-medium text-slate-500">@yield('page-subtitle', 'Kelola operasional laundry dengan kontrol akses yang rapi.')</p>
                            </div>
                        </div>
                        <div class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-soft sm:flex">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-extrabold text-white">
                                {{ strtoupper(str(auth()->user()?->nama)->substr(0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-ink">{{ auth()->user()?->nama }}</p>
                                <p class="text-xs font-semibold text-slate-400">{{ auth()->user()?->display_role }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                @if ($subscriptionStatus === 'expired')
                    <div class="mb-6 rounded-[28px] border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-800">
                        Langganan Anda telah habis. Anda masih bisa membuka dashboard dan billing, tetapi fitur transaksi serta fitur premium dikunci sampai subscription diperpanjang.
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('headerNotifications', () => ({
                open: false,
                count: 0,
                items: [],
                init() {
                    this.fetchNotifications();
                    // Polling setiap 30 detik untuk real-time update
                    setInterval(() => this.fetchNotifications(), 30000);
                },
                async fetchNotifications() {
                    try {
                        const res = await fetch('{{ route("api.notifications.due-transactions") }}');
                        const data = await res.json();
                        if (data.success) {
                            this.count = data.count;
                            this.items = data.items;
                        }
                    } catch (err) {
                        console.error('Error fetching notifications:', err);
                    }
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Temukan elemen header
            const header = document.querySelector('header');
            if (!header) return;

            // Cari kontainer aksi di bagian paling kanan header
            let actionsContainer = null;
            const firstChild = header.firstElementChild;
            
            // Jika header memiliki wrapper dalam (seperti layout default)
            if (firstChild && firstChild.classList.contains('justify-between')) {
                actionsContainer = firstChild.lastElementChild;
            } else {
                actionsContainer = header.lastElementChild;
            }

            if (actionsContainer) {
                const bellContainer = document.createElement('div');
                bellContainer.className = 'relative flex items-center';
                bellContainer.id = 'global-notification-bell-root';
                bellContainer.setAttribute('x-data', 'headerNotifications()');
                
                bellContainer.innerHTML = `
                    <!-- Tombol Lonceng -->
                    <button @click="open = !open" class="relative rounded-full p-2.5 text-slate-400 transition hover:bg-slate-100 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span x-show="count > 0" class="absolute top-1.5 right-1.5 flex h-4.5 w-4.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-extrabold text-white ring-2 ring-white animate-pulse" x-text="count" style="display: none;"></span>
                    </button>

                    <!-- Dropdown Modal -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-150" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-100" 
                         x-transition:leave-start="opacity-100 scale-100" 
                         x-transition:leave-end="opacity-0 scale-95" 
                         class="absolute right-0 mt-2 w-80 rounded-3xl border border-slate-100 bg-white p-4 shadow-panel z-50 top-full" 
                         style="display: none;">
                        <div class="flex items-center justify-between border-b border-slate-50 pb-3 mb-3">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Notifikasi Estimasi</h3>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500" x-text="count + ' tertunda'"></span>
                        </div>
                        <div class="max-h-60 overflow-y-auto space-y-2 pr-1">
                            <template x-for="item in items" :key="item.id">
                                <a :href="item.url" class="block rounded-2xl p-3 transition" :class="item.is_overdue ? 'bg-rose-50/50 hover:bg-rose-50' : 'bg-amber-50/50 hover:bg-amber-50'">
                                    <div class="flex items-start justify-between">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider" :class="item.is_overdue ? 'text-rose-700' : 'text-amber-700'" x-text="'#' + item.transaction_number"></span>
                                        <span class="text-[10px] font-extrabold" :class="item.is_overdue ? 'text-rose-600' : 'text-amber-600'" x-text="item.time_left"></span>
                                    </div>
                                    <p class="mt-1 text-xs font-extrabold text-slate-800" x-text="item.member_name"></p>
                                    <div class="mt-2 flex items-center justify-between text-[10px] font-semibold text-slate-400">
                                        <span x-text="'Estimasi Selesai: ' + item.estimated_finish"></span>
                                    </div>
                                    <div class="mt-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest text-right" x-text="item.outlet_name"></div>
                                </a>
                            </template>
                            <div x-show="count === 0" class="py-8 text-center">
                                <svg class="mx-auto h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 8v4l3 3"></path>
                                </svg>
                                <p class="mt-2 text-xs font-bold text-slate-400">Semua pesanan aman tepat waktu</p>
                            </div>
                        </div>
                    </div>
                `;

                // Jika container aksi adalah card profil, masukkan bell sebagai saudara sebelum profil card tersebut
                if (firstChild && firstChild.classList.contains('justify-between')) {
                    actionsContainer.parentNode.insertBefore(bellContainer, actionsContainer);
                } else {
                    // Di custom header (dashboard), masukkan ke dalam kontainer aksi paling kanan
                    actionsContainer.insertBefore(bellContainer, actionsContainer.firstChild);
                }

                // Compile Alpine tree
                if (window.Alpine) {
                    Alpine.initTree(bellContainer);
                } else {
                    document.addEventListener('alpine:init', () => {
                        Alpine.initTree(bellContainer);
                    });
                }
            }
        });
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PTPXVEN1H0"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-PTPXVEN1H0');
    </script>
</body>
</html>
