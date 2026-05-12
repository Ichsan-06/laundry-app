@extends('layouts.app')

@section('title', 'Machine Management - Laundry Track')

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
            <form action="{{ route('machines.index') }}" method="GET">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search machines by ID, Brand or Status..." 
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
                <p class="text-sm font-extrabold text-slate-900">Admin User</p>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Facility Manager</p>
            </div>
            <div class="h-10 w-10 overflow-hidden rounded-full bg-indigo-100 ring-2 ring-indigo-50 flex items-center justify-center">
                <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
        </div>
    </div>
</header>
@endsection

@section('content')
<div class="mx-auto max-w-[1400px] space-y-8">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Machine Management</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400">Manage your washing and drying units.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('machines.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Add Machine
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Available --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Status</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ str_pad($stats['available'], 2, '0', STR_PAD_LEFT) }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Available</p>
            </div>
        </div>

        {{-- In Use --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Status</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ str_pad($stats['in_use'], 2, '0', STR_PAD_LEFT) }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">In Use</p>
            </div>
        </div>

        {{-- Maintenance --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Status</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-slate-900">{{ str_pad($stats['maintenance'], 2, '0', STR_PAD_LEFT) }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Maintenance</p>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="group rounded-[24px] bg-indigo-600 p-6 shadow-lg shadow-indigo-500/20 transition duration-300 hover:shadow-xl text-white">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                        <path d="M12 12h.01"></path>
                        <path d="M17 12h.01"></path>
                        <path d="M7 12h.01"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-white/60">Today's Revenue</span>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</h3>
                <p class="text-xs font-bold text-white/60 uppercase tracking-widest mt-1">Daily Total</p>
            </div>
        </div>
    </div>

    {{-- Machines List --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        @foreach($machines as $machine)
        @php
            $statusColors = [
                'AVAILABLE' => 'emerald',
                'IN_USE' => 'amber',
                'MAINTENANCE' => 'rose',
                'FAULTY' => 'rose',
            ];
            $color = $statusColors[$machine->status] ?? 'slate';
        @endphp
        <div class="group relative overflow-hidden rounded-[28px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-400">
                        @if($machine->machine_type === 'WASHER')
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="3" width="12" height="18" rx="2"></rect><circle cx="12" cy="14" r="3"></circle></svg>
                        @else
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 7l7 5 7-5M5 17l7-5 7 5"></path></svg>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-lg font-extrabold text-slate-900">{{ $machine->machine_code }}</h4>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">{{ $machine->brand }} - {{ $machine->capacity_kg }}kg</p>
                    </div>
                </div>
                <span class="rounded-lg bg-{{ $color }}-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-widest text-{{ $color }}-600">{{ $machine->status }}</span>
            </div>

            <div class="mt-6 space-y-3">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Pricing Programs</p>
                <div class="space-y-2">
                    @foreach($machine->durations as $duration)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                        <span class="text-[11px] font-bold text-slate-600">{{ $duration->duration_type }} ({{ $duration->duration_minutes }}m)</span>
                        <span class="text-[11px] font-extrabold text-slate-900">Rp {{ number_format($duration->price, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions Overlay --}}
            <div class="mt-6 flex gap-2">
                <a href="{{ route('machines.edit', $machine->id) }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-slate-100 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-primary-50 hover:text-primary-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </a>
                <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this machine?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-slate-100 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-rose-50 hover:text-rose-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
