@extends('layouts.app')

@section('title', 'Dashboard - Laundry Coin')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Overview')

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-4 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
    <div class="min-w-0">
        <p class="text-[11px] font-extrabold uppercase tracking-widest text-primary-600">Overview</p>
        <h1 class="truncate text-xl font-extrabold tracking-tight text-slate-900 md:text-2xl">Dashboard Overview</h1>
    </div>
    <div class="flex items-center gap-4">
        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
            <div class="flex items-center gap-2 rounded-xl border border-slate-100 bg-white px-3 py-1.5 shadow-sm">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="bg-transparent text-sm font-bold text-slate-700 outline-none">
                <span class="text-slate-300">-</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="bg-transparent text-sm font-bold text-slate-700 outline-none">
                <button type="submit" class="ml-2 rounded-lg bg-slate-50 p-1.5 text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </button>
            </div>
        </form>
        
        <form action="{{ route('dashboard.export') }}" method="GET">
            <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/20 transition hover:bg-primary-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export Report
            </button>
        </form>
    </div>
</header>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-indigo-500">
                    +12.5%
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Total Revenue</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Machines Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                        <circle cx="12" cy="14" r="3"></circle>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-400">Capacity: {{ $machineCapacityPercent }}%</span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Active Machines</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $activeMachinesCount }}/{{ $totalMachines }} <span class="text-sm font-bold text-slate-400">Units</span></h3>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Pending Orders</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $pendingOrders }}</h3>
            </div>
        </div>

        <!-- New Members Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M20 8v6M23 11h-6"></path>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-indigo-500">
                    +{{ $newMembersCount }}
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">New Members</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $newMembersCount }} <span class="text-sm font-bold text-slate-400">today</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Live Machines -->
        <div class="lg:col-span-4 rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-extrabold text-slate-900">Live Machines</h3>
                <a href="{{ route('machines.index') }}" class="text-sm font-bold text-primary-600 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                @foreach($liveMachines as $machine)
                <div class="relative overflow-hidden rounded-2xl border {{ $machine['status'] === 'RUNNING' ? 'border-primary-100 bg-primary-50/30' : 'border-slate-100 bg-white' }} p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $machine['status'] === 'RUNNING' ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                                    <circle cx="12" cy="14" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-slate-900">{{ $machine['code'] }}</p>
                                <p class="text-[11px] font-bold text-slate-400">{{ str_replace('_', ' ', $machine['type']) }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold {{ $machine['status'] === 'RUNNING' ? 'text-primary-600' : 'text-slate-400' }}">{{ $machine['time_left'] }}</span>
                    </div>
                    @if($machine['status'] === 'RUNNING')
                    <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-primary-100">
                        <div class="h-full rounded-full bg-primary-600 transition-all duration-1000" style="width: {{ $machine['progress'] }}%"></div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Revenue Over Time -->
        <div class="lg:col-span-8 rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-extrabold text-slate-900">Revenue Over Time</h3>
                <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-1">
                    <button class="rounded-md bg-primary-600 px-3 py-1 text-[11px] font-bold text-white shadow-sm">Daily</button>
                    <button class="rounded-md px-3 py-1 text-[11px] font-bold text-slate-500 hover:bg-slate-200">Weekly</button>
                </div>
            </div>
            <div class="h-[280px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Recent Transactions -->
        <div class="lg:col-span-8 rounded-3xl border border-slate-100 bg-white shadow-soft overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-slate-900">Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm font-bold text-primary-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Order ID</th>
                            <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Service</th>
                            <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-8 py-4 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentTransactions as $t)
                        <tr class="group hover:bg-slate-50 transition">
                            <td class="px-8 py-5 text-sm font-bold text-slate-900">#ORD-{{ $t['id'] }}</td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-[10px] font-extrabold text-primary-600">
                                        {{ $t['customer_initials'] }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">{{ $t['customer'] }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-bold text-slate-500 capitalize">{{ $t['service'] }}</td>
                            <td class="px-8 py-5">
                                @if($t['status'] === 'COMPLETED')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Completed
                                    </span>
                                @elseif($t['status'] === 'IN_PROGRESS')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-3 py-1 text-[11px] font-extrabold text-primary-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-primary-500 animate-pulse"></span>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-extrabold text-amber-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right font-extrabold text-slate-900">Rp {{ number_format($t['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions & Alerts -->
        <div class="lg:col-span-4 space-y-6">
            <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
                <h3 class="text-lg font-extrabold text-slate-900 mb-6">Quick Actions</h3>
                <div class="space-y-4">
                    <a href="/kasir" class="flex items-center justify-between rounded-2xl bg-primary-600 p-5 text-white shadow-lg shadow-primary-500/30 transition hover:bg-primary-700 active:scale-95">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg>
                            </div>
                            <span class="font-extrabold">Create New Order</span>
                        </div>
                        <svg class="h-5 w-5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    
                    <button class="w-full flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5 text-slate-700 transition hover:bg-slate-50 active:scale-95">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <span class="font-extrabold">Add New Member</span>
                        </div>
                        <svg class="h-5 w-5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <button class="w-full flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5 text-slate-700 transition hover:bg-slate-50 active:scale-95">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                </svg>
                            </div>
                            <span class="font-extrabold">Machine Maintenance</span>
                        </div>
                        <svg class="h-5 w-5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="rounded-3xl bg-rose-50 p-8">
                <div class="flex items-center gap-4 text-rose-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <h4 class="font-extrabold uppercase tracking-wider text-rose-700">Alert: 2 Pending Issues</h4>
                </div>
                <p class="mt-4 text-sm font-semibold text-rose-600/80 leading-relaxed">
                    Washer #12 and Dryer #05 require immediate attention from the maintenance team.
                </p>
                <a href="#" class="mt-6 flex items-center gap-2 text-sm font-extrabold text-rose-700 hover:underline">
                    View Maintenance Log
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="fixed bottom-10 right-10 h-16 w-16 rounded-full bg-primary-600 text-white shadow-2xl shadow-primary-600/40 flex items-center justify-center hover:scale-110 active:scale-95 transition lg:hidden">
    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(109, 85, 232, 0.2)');
        gradient.addColorStop(1, 'rgba(109, 85, 232, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#6d55e8',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6d55e8',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, weight: '700' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
