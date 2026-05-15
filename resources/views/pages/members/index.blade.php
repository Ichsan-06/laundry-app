@extends('layouts.app')

@section('title', 'Member Management - Laundry Track')

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
            <form action="{{ route('members.index') }}" method="GET">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search members by nama, ID or phone..." 
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
    currentMember: null,
    deleteFormAction: '',
    confirmDelete(id) {
        if(confirm('Are you sure you want to delete this member?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    },
    openEditModal(member) {
        this.currentMember = member;
        this.showEditModal = true;
    }
}">
    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-600 ring-1 ring-emerald-100">
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Title & Add Button --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Member Management</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400 uppercase tracking-wider">Manage {{ number_format($stats['total_members']) }} registered customers and their loyalty status.</p>
        </div>
        <button @click="showAddModal = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700 active:scale-95">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
                <path d="M12 11v4M10 13h4"></path>
            </svg>
            Add New Member
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        {{-- Total Members --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600">+12%</span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Members</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_members']) }}</h3>
            </div>
        </div>

        {{-- Total Active Members --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <span class="text-xs font-bold text-emerald-600">Active</span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Active Members</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($stats['active_members']) }}</h3>
            </div>
        </div>


        {{-- New Members Today --}}
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Growth Rate</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">+5.4%</h3>
            </div>
        </div>
    </div>

    {{-- Main Content Table --}}
    <div class="rounded-[28px] bg-white shadow-soft ring-1 ring-slate-100">
        {{-- Table Toolbar --}}
        <div class="flex flex-col gap-4 border-b border-slate-50 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                {{-- Status Filter --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                        </svg>
                        Filter: {{ request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'All Status' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100">
                        <a href="{{ route('members.index', array_merge(request()->all(), ['status' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">All Status</a>
                        <a href="{{ route('members.index', array_merge(request()->all(), ['status' => 'ACTIVE'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Active</a>
                        <a href="{{ route('members.index', array_merge(request()->all(), ['status' => 'INACTIVE'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Inactive</a>
                        <a href="{{ route('members.index', array_merge(request()->all(), ['status' => 'PREMIUM'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Premium</a>
                    </div>
                </div>

                {{-- Sort Filter --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 16 4 4 4-4"></path>
                            <path d="M7 20V4"></path>
                            <path d="m21 8-4-4-4 4"></path>
                            <path d="M17 4v16"></path>
                        </svg>
                        Sort: {{ request('sort') == 'nama' ? 'Nama' : 'Latest' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 z-30 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100">
                        <a href="{{ route('members.index', array_merge(request()->all(), ['sort' => 'latest'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Latest</a>
                        <a href="{{ route('members.index', array_merge(request()->all(), ['sort' => 'nama'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Nama (A-Z)</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <p class="text-sm font-bold text-slate-400">Showing {{ $members->firstItem() }}-{{ $members->lastItem() }} of {{ $members->total() }}</p>
                <div class="flex gap-1">
                    <a href="{{ $members->previousPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-100 bg-white transition hover:bg-slate-50 {{ $members->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                    </a>
                    <a href="{{ $members->nextPageUrl() }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-100 bg-white transition hover:bg-slate-50 {{ !$members->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">
                        <th class="px-8 py-5">Member Nama</th>
                        <th class="px-6 py-5">Member ID</th>
                        <th class="px-6 py-5">Registered At</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($members as $member)
                    <tr class="group transition hover:bg-slate-50/50">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-bold text-white shadow-sm ring-2 ring-white
                                    {{ $loop->index % 5 == 0 ? 'bg-blue-400' : ($loop->index % 5 == 1 ? 'bg-slate-400' : ($loop->index % 5 == 2 ? 'bg-indigo-400' : ($loop->index % 5 == 3 ? 'bg-slate-500' : 'bg-blue-600'))) }}">
                                    {{ collect(explode(' ', $member->nama))->map(fn($n) => substr($n, 0, 1))->take(2)->join('') }}
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $member->nama }}</p>
                                    <p class="text-[13px] font-medium text-slate-400">{{ $member->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-bold tracking-wider text-slate-500 uppercase">{{ $member->id_member }}</span>
                        </td>

                        <td class="px-6 py-5">
                            <div>
                                <p class="text-[13px] font-extrabold text-slate-800">{{ $member->tanggal_daftar->format('M d, Y') }}</p>
                                <p class="text-[11px] font-bold text-slate-400">{{ $member->tanggal_daftar->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusClasses = [
                                    'ACTIVE' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                                    'INACTIVE' => 'bg-slate-100 text-slate-500 ring-slate-200',
                                    'PREMIUM' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
                                ];
                            @endphp
                            <span class="inline-flex rounded-md px-2 py-1 text-[10px] font-extrabold tracking-widest ring-1 ring-inset {{ $statusClasses[$member->status] ?? 'bg-slate-100 text-slate-500' }}">
                                {{ $member->status }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEditModal({{ json_encode($member) }})" class="rounded-lg p-2 text-slate-300 transition hover:bg-white hover:text-primary-600 hover:shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="confirmDelete({{ $member->id }})" class="rounded-lg p-2 text-slate-300 transition hover:bg-white hover:text-rose-600 hover:shadow-sm">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Member Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl ring-1 ring-slate-100" @click.away="showAddModal = false">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-slate-900">Add New Member</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form action="{{ route('members.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Full Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20 @error('nama') ring-2 ring-rose-500 @enderror">
                    @error('nama') <p class="text-[11px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20 @error('email') ring-2 ring-rose-500 @enderror">
                        @error('email') <p class="text-[11px] font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Phone</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20 @error('no_hp') ring-2 ring-rose-500 @enderror">
                        @error('no_hp') <p class="text-[11px] font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Status</label>
                        <select name="status" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-primary-500/20 @error('status') ring-2 ring-rose-500 @enderror">
                            <option value="ACTIVE" {{ old('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                            <option value="INACTIVE" {{ old('status') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                            <option value="PREMIUM" {{ old('status') == 'PREMIUM' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('status') <p class="text-[11px] font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showAddModal = false" class="flex-1 rounded-xl border-2 border-slate-100 px-6 py-3.5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Save Member</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Member Modal --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl ring-1 ring-slate-100" @click.away="showEditModal = false">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-slate-900">Edit Member</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form :action="'/members/' + currentMember?.id" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Full Nama</label>
                    <input type="text" name="nama" x-model="currentMember.nama" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Email</label>
                        <input type="email" name="email" x-model="currentMember.email" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Phone</label>
                        <input type="text" name="no_hp" x-model="currentMember.no_hp" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Status</label>
                        <select name="status" x-model="currentMember.status" required class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                            <option value="PREMIUM">Premium</option>
                        </select>
                    </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 rounded-xl border-2 border-slate-100 px-6 py-3.5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Update Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // If there are validation errors, reopen the add modal (or edit if we tracked which one)
    });
</script>
@endif
@endsection
