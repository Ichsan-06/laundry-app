<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laundry Coin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f4f1ff',
                            100: '#ebe5ff',
                            500: '#6d55e8',
                            600: '#5a43dc',
                            700: '#4935c7',
                        },
                    },
                    boxShadow: {
                        soft: '0 12px 34px rgba(31, 41, 55, 0.07)',
                        card: '0 8px 24px rgba(111, 85, 232, 0.08)',
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-[#f7f8fc] font-sans text-slate-800 antialiased">
    <div class="min-h-screen bg-white shadow-soft">
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-[270px] border-r border-slate-100 bg-white px-6 py-7 lg:flex lg:flex-col">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-[10px] bg-primary-600 text-white shadow-lg shadow-primary-500/25">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                        <path d="M8 7h2M14 7h2"></path>
                        <circle cx="12" cy="14" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-extrabold tracking-tight text-slate-900">Laundry Coin</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-400">Sistem Kasir</p>
                </div>
            </div>

            <nav class="mt-12 space-y-3">
                <a href="/kasir" class="flex items-center gap-4 rounded-lg {{ Request::is('kasir') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-slate-500 hover:bg-slate-50 hover:text-primary-600' }} px-5 py-4 text-[15px] font-bold transition">
                    <svg class="h-5 w-5 {{ Request::is('kasir') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 10h16v10H4z"></path>
                        <path d="M7 10V7a5 5 0 0 1 10 0v3"></path>
                    </svg>
                    Cashier
                </a>
                <a href="{{ route('machines.index') }}" class="flex items-center gap-4 rounded-lg {{ Request::routeIs('machines.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-slate-500 hover:bg-slate-50 hover:text-primary-600' }} px-5 py-4 text-[15px] font-bold transition">
                    <svg class="h-5 w-5 {{ Request::routeIs('machines.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                        <circle cx="12" cy="14" r="3"></circle>
                    </svg>
                    Machines
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-4 rounded-lg {{ Request::routeIs('transactions.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-slate-500 hover:bg-slate-50 hover:text-primary-600' }} px-5 py-4 text-[15px] font-bold transition">
                    <svg class="h-5 w-5 {{ Request::routeIs('transactions.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                        <path d="M8 8h8M8 12h8M8 16h4"></path>
                    </svg>
                    Transactions
                </a>
                <a href="{{ route('members.index') }}" class="flex items-center gap-4 rounded-lg {{ Request::routeIs('members.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-slate-500 hover:bg-slate-50 hover:text-primary-600' }} px-5 py-4 text-[15px] font-bold transition">
                    <svg class="h-5 w-5 {{ Request::routeIs('members.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Members
                </a>
                <a href="#" class="flex items-center gap-4 rounded-lg px-5 py-4 text-[15px] font-bold text-slate-500 transition hover:bg-slate-50 hover:text-primary-600">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <path d="M14 2v6h6M8 13h8M8 17h6"></path>
                    </svg>
                    Reports
                </a>
                <a href="#" class="flex items-center gap-4 rounded-lg px-5 py-4 text-[15px] font-bold text-slate-500 transition hover:bg-slate-50 hover:text-primary-600">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82 2 2 0 0 1-3.34 0A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33 2 2 0 0 1 0-3.34A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .33-1.82 2 2 0 0 1 3.34 0A1.65 1.65 0 0 0 15 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.23.36.58.64 1 .8a1.65 1.65 0 0 0 1.82-.33 2 2 0 0 1 0 3.34A1.65 1.65 0 0 0 19.4 15z"></path>
                    </svg>
                    Settings
                </a>
            </nav>

            <div class="mt-auto space-y-3">
                <button class="flex w-full items-center justify-between rounded-lg border border-slate-100 bg-white px-4 py-4 text-left shadow-card">
                    <span>
                        <span class="block text-xs font-bold text-slate-400">Outlet</span>
                        <span class="mt-1 block text-sm font-extrabold text-slate-800">Laundry Coin Center</span>
                    </span>
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <button class="flex w-full items-center gap-3 rounded-lg border border-slate-100 bg-white px-4 py-4 text-sm font-extrabold text-rose-500 shadow-card">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <path d="m16 17 5-5-5-5M21 12H9"></path>
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        <nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-slate-100 bg-white/95 px-2 py-2 shadow-[0_-12px_30px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden">
            <a href="#" class="flex flex-col items-center gap-1 rounded-xl bg-primary-50 py-2 text-[11px] font-extrabold text-primary-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 10h16v10H4z"></path>
                    <path d="M7 10V7a5 5 0 0 1 10 0v3"></path>
                </svg>
                Kasir
            </a>
            <a href="#" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[11px] font-bold text-slate-500">Transaksi</a>
            <a href="#" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[11px] font-bold text-slate-500">Mesin</a>
            <a href="#" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[11px] font-bold text-slate-500">Member</a>
            <a href="#" class="flex flex-col items-center gap-1 rounded-xl py-2 text-[11px] font-bold text-slate-500">Menu</a>
        </nav>

        <main class="flex min-h-screen min-w-0 flex-1 flex-col pb-20 lg:pb-0 lg:pl-[270px]">
            @sectionMissing('header')
            <header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-4 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
                <div class="min-w-0">
                    <div class="mb-1 flex items-center gap-3 lg:hidden">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-600 text-white shadow-lg shadow-primary-500/25">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                                <path d="M8 7h2M14 7h2"></path>
                                <circle cx="12" cy="14" r="4"></circle>
                            </svg>
                        </div>
                        <p class="truncate text-sm font-extrabold text-slate-900">Laundry Coin</p>
                    </div>
                    <h1 class="truncate text-xl font-extrabold tracking-tight text-slate-900 md:text-2xl">@yield('page-title', 'Kasir')</h1>
                    <p class="mt-1 text-sm font-semibold text-slate-400">@yield('page-subtitle', 'Buat Transaksi Baru')</p>
                </div>
                <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                    <button class="hidden items-center gap-3 rounded-lg border border-slate-100 bg-white px-4 py-3 text-sm font-extrabold text-slate-700 shadow-card md:flex">
                        <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                            <path d="M3 4v6h6"></path>
                        </svg>
                        Riwayat Transaksi
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg shadow-primary-500/30 md:h-12 md:w-12">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21a8 8 0 1 0-16 0"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-extrabold text-slate-900">Kasir 1</p>
                            <p class="text-xs font-semibold text-slate-400">Kasir</p>
                        </div>
                        <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </div>
                </div>
            </header>
            @else
                @yield('header')
            @endif

            <section class="flex-1 overflow-y-auto bg-[#fbfcff] p-5 md:p-8">
                @yield('content')
            </section>

            <footer class="hidden h-[96px] shrink-0 items-center gap-8 border-t border-slate-100 bg-white px-8 xl:flex">
                <button class="flex min-w-[210px] items-center gap-4 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-700 shadow-card">
                    <span class="rounded-md bg-emerald-50 px-3 py-2 text-emerald-500">F2</span>
                    Transaksi Baru
                </button>
                <button class="flex min-w-[210px] items-center gap-4 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-700 shadow-card">
                    <span class="rounded-md bg-primary-50 px-3 py-2 text-primary-600">F3</span>
                    Cari Member
                </button>
                <button class="flex min-w-[160px] items-center gap-4 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-700 shadow-card">
                    <span class="rounded-md bg-amber-50 px-3 py-2 text-amber-500">F4</span>
                    Diskon
                </button>
                <button class="flex min-w-[170px] items-center gap-4 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-700 shadow-card">
                    <span class="rounded-md bg-sky-50 px-3 py-2 text-sky-500">F5</span>
                    Catatan
                </button>
                <button class="ml-auto flex min-w-[150px] items-center gap-4 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-700 shadow-card">
                    <span class="rounded-md bg-rose-50 px-3 py-2 text-rose-500">Esc</span>
                    Batal
                </button>
            </footer>
        </main>
    </div>
</body>
</html>
