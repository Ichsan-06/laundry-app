@extends('layouts.app')

@section('title', 'Kalender Operasional - Laundry Track')

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-6 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
    <div class="flex flex-1 items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Kalender Operasional</h1>
            <p class="mt-1 text-sm font-semibold text-slate-400">Pantau pesanan harian</p>
        </div>
    </div>
    <div class="flex shrink-0 items-center gap-6">
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
<div class="mx-auto max-w-[1600px] space-y-8" x-data="{
    selectedDate: null,
    transactionsForDay: [],
    isLoadingDay: false,
    selectedOutlet: '{{ $selectedOutletId }}',
    selectedStatus: '{{ $selectedStatus }}',
    selectedServiceType: '{{ $selectedServiceType }}',
    
    async selectDay(date) {
        this.selectedDate = date;
        this.isLoadingDay = true;
        
        try {
            const response = await fetch(`/calendar/show/${date}?outlet_id=${this.selectedOutlet}&status=${this.selectedStatus}&service_type=${this.selectedServiceType}`);
            const data = await response.json();
            this.transactionsForDay = data.transactions;
        } catch (error) {
            console.error('Error loading transactions:', error);
            this.transactionsForDay = [];
        } finally {
            this.isLoadingDay = false;
        }
    },
    
    applyFilters() {
        const params = new URLSearchParams();
        params.set('month', '{{ $month }}');
        params.set('year', '{{ $year }}');
        params.set('outlet_id', this.selectedOutlet);
        params.set('status', this.selectedStatus);
        params.set('service_type', this.selectedServiceType);
        window.location.href = '/calendar?' + params.toString();
    },
    
    previousMonth() {
        let month = {{ $month }};
        let year = {{ $year }};
        month--;
        if (month < 1) {
            month = 12;
            year--;
        }
        const params = new URLSearchParams();
        params.set('month', month);
        params.set('year', year);
        params.set('outlet_id', this.selectedOutlet);
        params.set('status', this.selectedStatus);
        params.set('service_type', this.selectedServiceType);
        window.location.href = '/calendar?' + params.toString();
    },
    
    nextMonth() {
        let month = {{ $month }};
        let year = {{ $year }};
        month++;
        if (month > 12) {
            month = 1;
            year++;
        }
        const params = new URLSearchParams();
        params.set('month', month);
        params.set('year', year);
        params.set('outlet_id', this.selectedOutlet);
        params.set('status', this.selectedStatus);
        params.set('service_type', this.selectedServiceType);
        window.location.href = '/calendar?' + params.toString();
    }
}">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-5">
        {{-- Total Orders This Month --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                    <path d="M3 6h18"></path>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Order Bulan Ini</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_orders']) }}</h3>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Terlambat</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['terlambat']) }}</h3>
            </div>
        </div>

        {{-- Siap Diambil --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Siap Diambil</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['siap_diambil']) }}</h3>
            </div>
        </div>

        {{-- Pickup Pending --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 12h8M8 16h8M9 20h6a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Menunggu Jemput</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['pending_pickup']) }}</h3>
            </div>
        </div>

        {{-- Delivery Pending --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 12h8M8 16h8M9 20h6a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"></path>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Menunggu Antar</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['pending_delivery']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="flex flex-col gap-4 rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Outlet Filter (for admin/owner) --}}
            @if($outlets->count() > 1)
            <div class="relative">
                <select x-model="selectedOutlet" @change="applyFilters()" class="appearance-none rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 pr-10 cursor-pointer transition hover:bg-white hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </div>
            </div>
            @endif

            {{-- Status Filter --}}
            <div class="relative">
                <select x-model="selectedStatus" @change="applyFilters()" class="appearance-none rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 pr-10 cursor-pointer transition hover:bg-white hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @foreach($statusOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </div>
            </div>

            {{-- Service Type Filter --}}
            <div class="relative">
                <select x-model="selectedServiceType" @change="applyFilters()" class="appearance-none rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 pr-10 cursor-pointer transition hover:bg-white hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    @foreach($serviceTypeOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Month/Year Display --}}
        <div class="text-sm font-bold text-slate-500">
            {{ $currentDate->format('F Y') }}
        </div>
    </div>

    {{-- Main Calendar Section --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Calendar Grid (Left) --}}
        <div class="lg:col-span-2">
            <div class="rounded-[24px] bg-white shadow-soft ring-1 ring-slate-100 overflow-hidden">
                {{-- Month Navigation --}}
                <div class="flex items-center justify-between border-b border-slate-100 p-6">
                    <button @click="previousMonth()" class="flex h-10 w-10 items-center justify-center rounded-lg hover:bg-slate-50 transition">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                    </button>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $currentDate->format('F Y') }}</h2>
                    <button @click="nextMonth()" class="flex h-10 w-10 items-center justify-center rounded-lg hover:bg-slate-50 transition">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                </div>

                {{-- Calendar Grid --}}
                <div class="p-6">
                    {{-- Day Headers --}}
                    <div class="grid grid-cols-7 gap-2 mb-2">
                        @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                            <div class="text-center text-xs font-extrabold uppercase text-slate-400 py-2">
                                {{ $dayName }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar Days --}}
                    <div class="grid grid-cols-7 gap-2">
                        {{-- Empty cells for days before month starts --}}
                        @for($i = 0; $i < $firstDayOfWeek; $i++)
                            <div class="aspect-square rounded-xl bg-slate-50"></div>
                        @endfor

                        {{-- Days of month --}}
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dayData = $calendarData[$day];
                                $dateStr = $dayData['date'];
                                $transactionCounts = $dayData['counts'];
                                $isToday = $dateStr === now()->format('Y-m-d');
                                $transactions = $dayData['transactions'];
                            @endphp
                            <button @click="selectDay('{{ $dateStr }}')" 
                                    :class="selectedDate === '{{ $dateStr }}' ? 'ring-2 ring-primary-500 bg-primary-50' : ''"
                                    class="aspect-square rounded-xl bg-white border border-slate-200 p-2 text-left transition hover:shadow-md hover:border-slate-300 flex flex-col {{ $isToday ? 'ring-2 ring-primary-400' : '' }}">
                                <div class="text-xs font-extrabold {{ $isToday ? 'text-primary-600' : 'text-slate-900' }}">
                                    {{ $day }}
                                </div>
                                <div class="mt-1 flex-1 flex flex-col gap-0.5 justify-center">
                                    @php
                                        $visibleStatuses = ['PENDING', 'IN_PROGRESS', 'READY', 'COMPLETED'];
                                        $total = array_sum(array_intersect_key($transactionCounts, array_flip($visibleStatuses)));
                                    @endphp
                                    @if($total > 0)
                                        <div class="text-[10px] font-extrabold text-slate-600">
                                            {{ $total }} order{{ $total > 1 ? 's' : '' }}
                                        </div>
                                        <div class="flex flex-wrap gap-0.5">
                                            @foreach($visibleStatuses as $status)
                                                @if($transactionCounts[$status] > 0)
                                                    @php $color = $statusColors[$status] @endphp
                                                    <div class="h-1.5 flex-1 rounded-full {{ $color['dot'] }}"></div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[10px] font-semibold text-slate-300">
                                            —
                                        </div>
                                    @endif
                                </div>
                            </button>
                        @endfor
                    </div>

                    {{-- Legend --}}
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-xs font-extrabold uppercase text-slate-400 mb-3">Legenda Status</p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['PENDING', 'IN_PROGRESS', 'READY', 'COMPLETED'] as $status)
                                @php $color = $statusColors[$status] @endphp
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-2.5 rounded-full {{ $color['dot'] }}"></div>
                                    <span class="text-xs font-bold text-slate-600">{{ $statusLabels[$status] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Day Transactions Panel (Right) --}}
        <div class="lg:col-span-1">
            <div class="rounded-[24px] bg-white shadow-soft ring-1 ring-slate-100 overflow-hidden h-full flex flex-col">
                {{-- Header --}}
                <div class="border-b border-slate-100 p-6">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Rincian Pesanan</p>
                    <h3 class="mt-2 text-lg font-extrabold text-slate-900">
                        <span x-text="selectedDate ? new Date(selectedDate).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'Pilih Tanggal'"></span>
                    </h3>
                </div>

                {{-- Transactions List --}}
                <div class="flex-1 overflow-y-auto p-6" x-cloak>
                    <template x-if="selectedDate === null">
                        <div class="text-center py-8">
                            <svg class="h-12 w-12 mx-auto mb-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M8 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2M8 4v4m4-4v4m4-4v4M6 12h12"></path>
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Pilih tanggal untuk melihat pesanan</p>
                        </div>
                    </template>

                    <template x-if="selectedDate && isLoadingDay">
                        <div class="text-center py-8">
                            <div class="inline-flex h-8 w-8 animate-spin rounded-full border-4 border-slate-300 border-t-primary-500"></div>
                            <p class="mt-2 text-sm font-semibold text-slate-400">Memuat pesanan...</p>
                        </div>
                    </template>

                    <template x-if="selectedDate && !isLoadingDay">
                        <div x-show="transactionsForDay.length === 0" class="text-center py-8">
                            <svg class="h-12 w-12 mx-auto mb-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Tidak ada pesanan</p>
                        </div>
                    </template>

                    <div x-show="selectedDate && transactionsForDay.length > 0" class="space-y-3">
                        <template x-for="transaction in transactionsForDay" :key="transaction.id">
                            <a :href="transaction.detail_url" class="block rounded-lg border border-slate-100 p-3 hover:border-primary-200 hover:bg-primary-50/50 transition">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                                            <span x-text="transaction.transaction_number"></span>
                                        </p>
                                        <p class="mt-1 text-sm font-bold text-slate-900 truncate" x-text="transaction.member_name"></p>
                                    </div>
                                    <span :class="transaction.status_color.ring + ' ' + transaction.status_color.bg + ' ' + transaction.status_color.text" class="inline-flex shrink-0 rounded-full px-2 py-1 text-xs font-extrabold ring-1">
                                        <span x-text="transaction.status_label"></span>
                                    </span>
                                </div>
                                <div class="space-y-1 text-xs text-slate-500">
                                    <p class="flex justify-between">
                                        <span x-text="transaction.service_type_label"></span>
                                        <span class="font-bold text-slate-600" x-text="transaction.transaction_type_label"></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span x-text="'Rp. ' + transaction.total_amount"></span>
                                        <span class="text-slate-400" x-text="transaction.created_at"></span>
                                    </p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Auto-select today if no date is selected
    document.addEventListener('Alpine.init', function() {
        // This will auto-initialize Alpine component
    });
</script>
@endsection
