@extends('layouts.app')

@section('title', 'Laporan - Laundry Coin')
@section('page-title', 'Reports (Laporan)')
@section('page-subtitle', 'Performance analysis for ' . $startDate->format('M d') . ' - ' . $endDate->format('M d, Y'))

@section('content')
<div class="space-y-8">
    <!-- Filters & Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1 rounded-xl bg-white p-1 shadow-sm border border-slate-100">
            <a href="{{ route('reports.index', ['filter' => 'monthly']) }}" class="rounded-lg {{ $filter === 'monthly' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50' }} px-4 py-2 text-sm font-bold transition">Monthly</a>
            <a href="{{ route('reports.index', ['filter' => 'weekly']) }}" class="rounded-lg {{ $filter === 'weekly' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50' }} px-4 py-2 text-sm font-bold transition">Weekly</a>
            <a href="{{ route('reports.index', ['filter' => 'daily']) }}" class="rounded-lg {{ $filter === 'daily' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-slate-500 hover:bg-slate-50' }} px-4 py-2 text-sm font-bold transition">Daily</a>
            <div class="mx-2 h-4 w-px bg-slate-200"></div>
            <button class="rounded-lg p-2 text-slate-500 hover:bg-slate-50">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft transition hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    12%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Total Revenue</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft transition hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    8.4%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Transactions</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Efficiency Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft transition hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-rose-500">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                    2.1%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Avg. Efficiency</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($avgEfficiency, 1) }}%</h3>
            </div>
        </div>

        <!-- New Members Card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-soft transition hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-xs font-bold text-emerald-500">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    15%
                </span>
            </div>
            <div class="mt-4">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">New Members</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($newMembers, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts & Breakdown -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Revenue Growth Chart -->
        <div class="lg:col-span-2 rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Revenue Growth</h3>
                    <p class="text-sm font-semibold text-slate-400">Daily earnings trajectory for the current month</p>
                </div>
                <button class="flex items-center gap-2 text-sm font-bold text-primary-600 hover:underline">
                    Download CSV
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </button>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Service Usage Breakdown -->
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
            <h3 class="text-lg font-extrabold text-slate-900">Service Usage</h3>
            <p class="text-sm font-semibold text-slate-400 mb-8">Breakdown of service preferences</p>
            
            <div class="space-y-6">
                @foreach($serviceUsage as $usage)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-700">{{ str_replace('_', ' ', $usage['name']) }}</span>
                        <span class="text-sm font-bold text-slate-400">{{ $usage['percentage'] }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-primary-500 transition-all duration-1000" style="width: {{ $usage['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 rounded-2xl bg-slate-50 p-6">
                <p class="text-sm font-medium italic text-slate-500 text-center">
                    "Wash & Fold continues to be the primary revenue driver, up 4% from last month."
                </p>
            </div>
        </div>
    </div>

    <!-- Machine Statistics Table -->
    <div class="rounded-3xl border border-slate-100 bg-white shadow-soft overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-slate-900">Machine Statistics</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-400">Sort by:</span>
                    <select class="bg-transparent text-sm font-extrabold text-slate-900 outline-none cursor-pointer">
                        <option>Usage Frequency</option>
                        <option>Revenue</option>
                        <option>Efficiency</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Machine ID</th>
                        <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Type</th>
                        <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Cycles (Mo)</th>
                        <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Avg. Duration</th>
                        <th class="px-8 py-4 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-8 py-4 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($machineStats as $stat)
                    <tr class="group transition hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400 group-hover:bg-primary-50 group-hover:text-primary-600 transition">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                                        <circle cx="12" cy="14" r="3"></circle>
                                    </svg>
                                </div>
                                <span class="font-extrabold text-slate-900">{{ $stat['code'] }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-500">{{ $stat['type'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-900">{{ $stat['cycles'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-500">{{ $stat['avg_duration'] }}m</td>
                        <td class="px-8 py-5">
                            @if($stat['status'] == 'READY')
                                <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-[11px] font-extrabold text-emerald-600">Optimal</span>
                            @else
                                <span class="rounded-lg bg-amber-50 px-3 py-1.5 text-[11px] font-extrabold text-amber-600">Service Due</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right font-extrabold text-slate-900">Rp {{ number_format($stat['revenue'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-8 bg-slate-50/30 flex justify-center">
            <button class="flex items-center gap-2 text-sm font-extrabold text-primary-600 hover:underline">
                View Detailed Machine Analysis
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Floating Action Button (Matches Reference) -->
<button class="fixed bottom-10 right-10 h-14 w-14 rounded-full bg-primary-600 text-white shadow-2xl shadow-primary-600/40 flex items-center justify-center hover:scale-110 active:scale-95 transition lg:bottom-24 lg:right-12">
    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
</button>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient for bars
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, '#6d55e8');
        gradient.addColorStop(1, '#a89bff');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        
                        // Highlights every 7th bar like in the reference
                        return context.dataIndex % 7 === 0 ? '#6d55e8' : '#f1f5f9';
                    },
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            color: '#94a3b8',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
