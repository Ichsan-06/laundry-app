@php
    $mainMenus = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard*', 'permission' => 'view dashboard'],
        ['label' => 'Kasir', 'route' => 'kasir.index', 'match' => 'kasir*', 'permission' => 'access cashier'],
        ['label' => 'Member', 'route' => 'members.index', 'match' => 'members*', 'permission' => 'manage members'],
        ['label' => 'Transaksi', 'route' => 'transactions.index', 'match' => 'transactions*', 'permission' => 'manage transactions'],
        ['label' => 'Mesin', 'route' => 'machines.index', 'match' => 'machines*', 'permission' => 'manage machines'],
        ['label' => 'Layanan', 'route' => 'services.index', 'match' => 'services*', 'permission' => 'manage services'],
        ['label' => 'Addon', 'route' => 'addons.index', 'match' => 'addons*', 'permission' => 'manage addons'],
        ['label' => 'Laporan', 'route' => 'reports.index', 'match' => 'reports*', 'permission' => 'view reports'],
        ['label' => 'Pengguna', 'route' => 'users.index', 'match' => 'users*', 'permission' => 'manage users'],
    ];

    $systemMenus = [
        ['label' => 'Roles', 'route' => 'roles.index', 'match' => 'roles*', 'permission' => 'manage roles'],
        ['label' => 'Permissions', 'route' => 'permissions.index', 'match' => 'permissions*', 'permission' => 'manage permissions'],
        ['label' => 'Settings', 'route' => 'settings.index', 'match' => 'settings*', 'permission' => 'manage settings'],
    ];

    $outletName = \App\Models\Outlet::first()?->nama_outlet ?? 'Laundry Control';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laundry Control')</title>
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
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-soft">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="5" y="3" width="14" height="18" rx="3"></rect>
                        <circle cx="12" cy="13" r="4"></circle>
                        <path d="M8 7h2M14 7h2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-ink">{{ $outletName }}</p>
                    <p class="text-sm font-semibold text-slate-400">RBAC Laundry Panel</p>
                </div>
            </div>

            <div class="mt-8 rounded-3xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">Signed In</p>
                <p class="mt-2 text-base font-extrabold text-ink">{{ auth()->user()?->nama }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">{{ auth()->user()?->display_role }}</span>
                    @role('Super Admin')
                        <span class="rounded-full bg-accent-100 px-3 py-1 text-xs font-bold text-amber-700">Root Access</span>
                    @endrole
                </div>
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

                @canany(['manage roles', 'manage permissions', 'manage settings'])
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
</body>
</html>
