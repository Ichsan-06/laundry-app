@extends('layouts.app')

@section('title', 'Transaksi - Laundry Track')

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-6 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
    <div class="flex flex-1 items-center gap-4">
        <div class="relative w-full max-w-[500px]">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </div>
            <form action="{{ route('transactions.index') }}" method="GET">
                {{-- Keep existing filters when searching --}}
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('transaction_type')) <input type="hidden" name="transaction_type" value="{{ request('transaction_type') }}"> @endif
                @if(request('service_type')) <input type="hidden" name="service_type" value="{{ request('service_type') }}"> @endif
                @if(request('payment_method')) <input type="hidden" name="payment_method" value="{{ request('payment_method') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari berdasarkan ID, Nama Member..." 
                    class="block w-full rounded-xl border-none bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
            </form>
        </div>
    </div>
    <div class="flex shrink-0 items-center gap-6">
        {{-- <button class="relative rounded-full p-2 text-slate-400 transition hover:bg-slate-50 hover:text-primary-600">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
            </svg>
            <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
        </button> --}}
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-extrabold text-slate-900">Marcus Reed</p>
                <p class="text-[11px] font-bold text-slate-400">Super Admin</p>
            </div>
            <div class="h-10 w-10 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-50">
                <img src="https://ui-avatars.com/api/?name=Marcus+Reed&background=6d55e8&color=fff" alt="Marcus Reed">
            </div>
        </div>
    </div>
</header>
@endsection

@section('content')
<div class="mx-auto max-w-[1400px] space-y-8" x-data="{ 
    showAddModal: false, 
    showEditModal: false,
    currentTrx: null,
    confirmDelete(id) {
        if(confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    },
    openEditModal(trx) {
        this.currentTrx = trx;
        this.showEditModal = true;
    }
}">
@php
    $serviceMap = [
        'WASH_ONLY' => 'Cuci Saja',
        'DRY_ONLY' => 'Kering Saja',
        'WASH_DRY' => 'Cuci & Kering',
        'IRONING' => 'Setrika',
        'COMPLETE' => 'Komplit',
    ];
    $typeMap = [
        'SELF_SERVICE' => 'Mandiri',
        'DROP_OFF' => 'Drop Off',
    ];
    $statusMap = [
        'PENDING' => 'Menunggu',
        'IN_PROGRESS' => 'Diproses',
        'READY' => 'Siap Diambil',
        'COMPLETED' => 'Selesai',
        'CANCELLED' => 'Dibatalkan',
    ];
@endphp
    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-600 ring-1 ring-emerald-100">
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Title & Add Button --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Transaksi</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400 uppercase tracking-wider">Ikhtisar semua pesanan laundry dan pembayaran.</p>
        </div>
        <!-- <button @click="showAddModal = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700 active:scale-95">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            New Transaction
        </button> -->
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Revenue --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600">+12.5%</span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Pendapatan</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">Rp. {{ number_format($stats['total_revenue'], 2) }}</h3>
            </div>
        </div>

        {{-- Orders --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                    <path d="M3 6h18"></path>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Pesanan</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_orders']) }}</h3>
            </div>
        </div>

        {{-- Active Orders --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Aktif</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['active_orders']) }}</h3>
            </div>
        </div>

        {{-- Completed Today --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Selesai Hari Ini</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['completed_today']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Tabs & Filters --}}
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        {{-- Custom Tabs --}}
        <div class="flex items-center gap-1 rounded-[18px] bg-slate-100 p-1.5 shadow-inner ring-1 ring-slate-200/50">
            @php
                $activeTab = request('transaction_type', 'all');
                $tabs = [
                    'all' => ['label' => 'Semua Pesanan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z'],
                    'SELF_SERVICE' => ['label' => 'Mandiri (Self Service)', 'icon' => 'M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41'],
                    'DROP_OFF' => ['label' => 'Drop Off', 'icon' => 'M21 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2M17 12h.01M11 12h.01M14 12h.01M7 12h.01']
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('transactions.index', array_merge(request()->all(), ['transaction_type' => $key])) }}" 
                   class="flex items-center gap-2.5 rounded-[14px] px-5 py-2.5 text-[13px] font-extrabold transition-all duration-300 {{ $activeTab === $key ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="{{ $tab['icon'] }}"></path></svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <p class="text-sm font-bold text-slate-400">Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }}</p>
            <div class="flex gap-1">
                <a href="{{ $transactions->previousPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-100 bg-white transition hover:bg-slate-50 {{ $transactions->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </a>
                <a href="{{ $transactions->nextPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-100 bg-white transition hover:bg-slate-50 {{ !$transactions->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content Table --}}
    <div class="rounded-[28px] bg-white shadow-soft ring-1 ring-slate-100">
        {{-- Table Toolbar --}}
        <div class="flex flex-col gap-4 border-b border-slate-50 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Filter: Status --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                        </svg>
                        Status: {{ request('status') ? str_replace('_', ' ', request('status')) : 'Semua' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                        @foreach(['all', 'PENDING', 'IN_PROGRESS', 'READY', 'COMPLETED', 'CANCELLED'] as $status)
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['status' => $status])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            {{ $status == 'all' ? 'Semua Status' : str_replace('_', ' ', $status) }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Filter: Service Type --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        Layanan: {{ request('service_type') && request('service_type') !== 'all' ? ($servicePackages->firstWhere('id', request('service_type'))?->nama_paket ?? ($serviceMap[request('service_type')] ?? str_replace('_', ' ', request('service_type')))) : 'Semua' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-64 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100 max-h-64 overflow-y-auto" x-cloak>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['service_type' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            Semua Layanan
                        </a>
                        <div class="my-1 border-t border-slate-50"></div>
                        <p class="px-4 py-1 text-[10px] font-black uppercase tracking-widest text-slate-400">Tipe Layanan</p>
                        @foreach(['WASH_ONLY', 'DRY_ONLY', 'WASH_DRY', 'IRONING', 'COMPLETE'] as $st)
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['service_type' => $st])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            {{ $serviceMap[$st] ?? str_replace('_', ' ', $st) }}
                        </a>
                        @endforeach
                        <div class="my-1 border-t border-slate-50"></div>
                        <p class="px-4 py-1 text-[10px] font-black uppercase tracking-widest text-slate-400">Paket Layanan</p>
                        @foreach($servicePackages as $pkg)
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['service_type' => $pkg->id])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            {{ $pkg->nama_paket }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Filter: Outlet (Only for Owner/Admin) --}}
                @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Outlet: {{ request('outlet_id') && request('outlet_id') !== 'all' ? ($outlets->firstWhere('id', request('outlet_id'))?->nama_outlet ?? 'Semua') : 'Semua' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['outlet_id' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            Semua Outlet
                        </a>
                        @foreach($outlets as $outlet)
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['outlet_id' => $outlet->id])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">
                            {{ $outlet->nama_outlet }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Sort --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 16 4 4 4-4"></path>
                            <path d="M7 20V4"></path>
                            <path d="m21 8-4-4-4 4"></path>
                            <path d="M17 4v16"></path>
                        </svg>
                        Urutkan: {{ request('sort') ? str_replace('_', ' ', request('sort')) : 'Terbaru' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'latest'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Terbaru</a>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'oldest'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Terlama</a>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'highest_amount'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Total Tertinggi</a>
                        <a href="{{ route('transactions.index', array_merge(request()->all(), ['sort' => 'lowest_amount'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Total Terendah</a>
                    </div>
                </div>

                @if(request()->anyFilled(['status', 'transaction_type', 'service_type', 'payment_method', 'sort', 'search', 'outlet_id']))
                <a href="{{ route('transactions.index') }}" class="text-xs font-extrabold text-rose-500 hover:underline">Hapus Semua Filter</a>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">
                        <th class="px-8 py-5">ID Pesanan</th>
                        <th class="px-6 py-5">Pelanggan / Member</th>
                        <th class="px-6 py-5">Layanan</th>
                        <th class="px-6 py-5">Pembayaran</th>
                        <th class="px-6 py-5 text-right">Total</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $trx)
                    <tr class="group transition hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <span class="text-sm font-extrabold text-slate-900">{{ $trx->transaction_number }}</span>
                            <p class="text-[11px] font-bold text-slate-400">{{ $trx->created_at->format('M d, H:i A') }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 overflow-hidden rounded-full bg-slate-100">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($trx->member?->nama ?? 'Guest') }}&background=f1f5f9&color=64748b" alt="">
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $trx->member?->nama ?? 'Tamu' }}</p>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $trx->cashier?->nama ?? 'Sistem' }} (Kasir)</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-600">{{ $serviceMap[$trx->service_type] ?? str_replace('_', ' ', $trx->service_type) }}</span>
                                <span class="text-[9px] font-extrabold uppercase tracking-widest {{ $trx->transaction_type === 'SELF_SERVICE' ? 'text-indigo-400' : 'text-emerald-400' }}">
                                    {{ $typeMap[$trx->transaction_type] ?? str_replace('_', ' ', $trx->transaction_type) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-[13px] font-bold text-slate-600">{{ $trx->payment_method }}</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-[15px] font-extrabold text-slate-900">Rp. {{ number_format($trx->total_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusClasses = [
                                    'PENDING' => 'bg-amber-50 text-amber-600 ring-amber-100',
                                    'IN_PROGRESS' => 'bg-blue-50 text-blue-600 ring-blue-100',
                                    'READY' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
                                    'COMPLETED' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                                    'CANCELLED' => 'bg-rose-50 text-rose-600 ring-rose-100',
                                ];
                            @endphp
                            <span class="inline-flex rounded-md px-2 py-1 text-[10px] font-extrabold tracking-widest ring-1 ring-inset {{ $statusClasses[$trx->status] ?? 'bg-slate-100 text-slate-500' }}">
                                {{ $statusMap[$trx->status] ?? str_replace('_', ' ', $trx->status) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('transactions.show', $trx->id) }}" class="rounded-lg p-2 text-slate-300 transition hover:bg-white hover:text-indigo-600 hover:shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>

                                @if($trx->status === 'READY' && $trx->transaction_type === 'DROP_OFF' && $trx->member && $trx->member->no_hp)
                                    @php
                                        $waMessage = "Halo *" . ($trx->member->nama ?? 'Pelanggan') . "*,\n\n";
                                        $waMessage .= "Cucian Anda dengan No. Transaksi *" . $trx->transaction_number . "* sudah *Selesai & Siap Diambil* di *" . $trx->outlet->nama_outlet . "*.\n\n";
                                        $waMessage .= "💰 *Total Tagihan: Rp " . number_format($trx->total_amount, 0, ',', '.') . "*\n\n";
                                        $waMessage .= "Silakan datang ke outlet untuk pengambilan. Terima kasih! 🙏✨";
                                        
                                        $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $trx->member->no_hp));
                                        $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waMessage);
                                    @endphp
                                    <a href="{{ $waUrl }}" target="_blank" class="rounded-lg p-2 text-emerald-400 transition hover:bg-white hover:text-emerald-600 hover:shadow-sm" title="Kirim Pengingat WA">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-11.7 8.38 8.38 0 0 1 3.8.9L21 3z"></path>
                                        </svg>
                                    </a>
                                @endif

                                <button @click="openEditModal({{ json_encode($trx) }})" class="rounded-lg p-2 text-slate-300 transition hover:bg-white hover:text-primary-600 hover:shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <form id="delete-form-{{ $trx->id }}" action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="confirmDelete('{{ $trx->id }}')" class="rounded-lg p-2 text-slate-300 transition hover:bg-white hover:text-rose-600 hover:shadow-sm">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="h-16 w-16 text-slate-200">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-400">Tidak ada transaksi yang cocok dengan filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Transaction Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] w-full max-w-2xl overflow-hidden shadow-2xl ring-1 ring-slate-100" @click.away="showAddModal = false">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-slate-900">Transaksi Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST" class="p-8 space-y-6 overflow-y-auto max-h-[80vh]">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Outlet</label>
                        <select name="outlet_id" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Kasir</label>
                        <select name="cashier_id" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            @foreach($cashiers as $cashier)
                            <option value="{{ $cashier->id }}">{{ $cashier->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Member (Optional)</label>
                        <select name="member_id" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            <option value="">Tamu</option>
                            @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Tipe Layanan</label>
                        <select name="service_type" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            <option value="WASH_ONLY">Cuci Saja</option>
                            <option value="DRY_ONLY">Kering Saja</option>
                            <option value="WASH_DRY">Cuci & Kering</option>
                            <option value="IRONING">Setrika</option>
                            <option value="COMPLETE">Komplit</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Subtotal</label>
                        <input type="number" step="0.01" name="subtotal" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Diskon</label>
                        <input type="number" step="0.01" name="member_discount" value="0" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Jumlah Diterima</label>
                        <input type="number" step="0.01" name="amount_received" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Metode Pembayaran</label>
                        <select name="payment_method" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            <option value="CASH">Tunai</option>
                            <option value="TRANSFER">Transfer</option>
                            <option value="E_WALLET">E-Wallet</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Status</label>
                        <select name="status" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                            <option value="PENDING">Menunggu</option>
                            <option value="IN_PROGRESS">Diproses</option>
                            <option value="COMPLETED">Selesai</option>
                            <option value="CANCELLED">Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="notes" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900"></textarea>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showAddModal = false" class="flex-1 rounded-xl border-2 border-slate-100 px-6 py-3.5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Transaction Modal --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl ring-1 ring-slate-100" @click.away="showEditModal = false">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-slate-900">Update Transaksi</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form :action="'/transactions/' + currentTrx?.id" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Status</label>
                    <select name="status" x-model="currentTrx.status" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                        <option value="PENDING">Menunggu</option>
                        <option value="IN_PROGRESS">Diproses</option>
                        <template x-if="currentTrx.transaction_type === 'DROP_OFF'">
                            <option value="READY">Siap Diambil</option>
                        </template>
                        <option value="COMPLETED">Selesai</option>
                        <option value="CANCELLED">Dibatalkan</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Metode Pembayaran</label>
                    <select name="payment_method" x-model="currentTrx.payment_method" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                        <option value="CASH">Tunai</option>
                        <option value="TRANSFER">Transfer</option>
                        <option value="E_WALLET">E-Wallet</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="notes" x-model="currentTrx.notes" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900"></textarea>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 rounded-xl border-2 border-slate-100 px-6 py-3.5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
