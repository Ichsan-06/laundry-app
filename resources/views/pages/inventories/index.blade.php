@extends('layouts.app')

@section('title', 'Inventaris - Laundry Track')

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
            <form action="{{ route('inventories.index') }}" method="GET">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('outlet_id')) <input type="hidden" name="outlet_id" value="{{ request('outlet_id') }}"> @endif
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama barang..."
                    class="block w-full rounded-xl border-none bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
            </form>
        </div>
    </div>
</header>
@endsection

@section('content')
<div x-data="inventoryPage" class="mx-auto max-w-[1400px] space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Inventaris</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400">Pantau stok bahan laundry agar operasional tetap lancar.</p>
        </div>
        <button type="button" @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            Tambah Barang
        </button>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m21 16-9 5-9-5V8l9-5 9 5v8Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 12v9"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Item</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['total_items'] }} <span class="text-lg font-bold text-slate-400">barang</span></h3>
            </div>
        </div>

        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m21 16-9 5-9-5V8l9-5 9 5v8Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 12v9"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Stok Menipis</p>
                <h3 class="mt-1 text-3xl font-extrabold {{ $stats['low_stock_count'] > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $stats['low_stock_count'] }} <span class="text-lg font-bold text-slate-400">item</span></h3>
                <p class="mt-1 text-sm font-semibold text-slate-400">{{ $stats['low_stock_count'] > 0 ? 'Segera lakukan restock.' : 'Semua aman.' }}</p>
            </div>
        </div>

        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m21 16-9 5-9-5V8l9-5 9 5v8Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 12v9"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Stok Aman</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['safe_stock_count'] }} <span class="text-lg font-bold text-slate-400">item</span></h3>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                    Status: {{ request('status') === 'low' ? 'Stok Menipis' : (request('status') === 'safe' ? 'Stok Aman' : 'Semua') }}
                </button>
                <div x-show="open" @click.away="open = false" class="absolute left-0 z-30 mt-2 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                    <a href="{{ route('inventories.index', array_merge(request()->all(), ['status' => null])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Semua</a>
                    <a href="{{ route('inventories.index', array_merge(request()->all(), ['status' => 'low'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Stok Menipis</a>
                    <a href="{{ route('inventories.index', array_merge(request()->all(), ['status' => 'safe'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Stok Aman</a>
                </div>
            </div>

            @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        Cabang: {{ request('outlet_id') && request('outlet_id') !== 'all' ? ($outlets->firstWhere('id', request('outlet_id'))?->nama_outlet ?? 'Semua') : 'Semua' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 z-30 mt-2 w-56 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                        <a href="{{ route('inventories.index', array_merge(request()->all(), ['outlet_id' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Semua Cabang</a>
                        @foreach($outlets as $outlet)
                            <a href="{{ route('inventories.index', array_merge(request()->all(), ['outlet_id' => $outlet->id])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">{{ $outlet->nama_outlet }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(request()->anyFilled(['search', 'status', 'outlet_id']))
                <a href="{{ route('inventories.index') }}" class="text-xs font-extrabold text-rose-500 hover:underline">Hapus Filter</a>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-[32px] bg-white shadow-soft ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/30">
                        <th class="px-8 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Nama Barang</th>
                        <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Satuan</th>
                        <th class="px-6 py-5">Cabang</th>
                        <th class="px-6 py-5 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Stok</th>
                        <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-8 py-5 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($inventories as $inventory)
                        <tr class="group transition hover:bg-slate-50/50">
                            <td class="px-8 py-5">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $inventory->nama_barang }}</p>
                                    <p class="text-xs font-medium text-slate-400">{{ $inventory->catatan ?: '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-slate-600">{{ strtoupper($inventory->satuan) }}</td>
                            <td class="px-6 py-5 text-sm font-bold text-slate-500">{{ $inventory->outlet?->nama_outlet ?? '-' }}</td>
                            <td class="px-6 py-5 text-right">
                                <p class="text-sm font-extrabold text-slate-900">{{ $inventory->formattedQuantity() }} <span class="font-bold text-slate-400">{{ $inventory->satuan }}</span></p>
                            </td>
                            <td class="px-6 py-5">
                                @if($inventory->isLowStock())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-extrabold text-amber-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openRestockModal(findInventory('{{ $inventory->id }}'))" class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-extrabold text-white transition hover:bg-primary-700">Restock</button>
                                    <button type="button" @click="openUsageModal(findInventory('{{ $inventory->id }}'))" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50">Pakai</button>
                                    <button type="button" @click="openHistoryModal(findInventory('{{ $inventory->id }}'))" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-primary-50 hover:text-primary-600">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                            <path d="M3 3v6h6"></path>
                                            <path d="M12 7v5l3 3"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="openEditModal(findInventory('{{ $inventory->id }}'))" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-primary-50 hover:text-primary-600">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                            <td colspan="5" class="px-8 py-16 text-center text-sm font-bold text-slate-400">Belum ada barang inventaris.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $inventories->links() }}
        </div>
    </div>

    <div x-show="modal === 'create'" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Tambah Barang Baru</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('inventories.store') }}" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Nama Barang</label>
                    <input type="text" name="nama_barang" required placeholder="Contoh: Deterjen Bubuk" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Satuan</label>
                        <select name="satuan" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            @foreach($unitOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Alert Stok ≤</label>
                        <input type="number" step="0.01" name="alert_stok" required value="5" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Stok Awal</label>
                        <input type="number" step="0.01" name="stok_awal" value="0" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Cabang</label>
                        <select name="outlet_id" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="catatan" rows="3" placeholder="Catatan tambahan barang..." class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modal === 'edit' && currentItem" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Edit Barang</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form :action="inventoryAction(currentItem.id)" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Nama Barang</label>
                    <input type="text" name="nama_barang" x-model="currentItem.nama_barang" required class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Satuan</label>
                        <select name="satuan" x-model="currentItem.satuan" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            @foreach($unitOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Alert Stok ≤</label>
                        <input type="number" step="0.01" name="alert_stok" x-model="currentItem.alert_stok" required class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Cabang</label>
                        <select name="outlet_id" x-model="currentItem.outlet_id" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Stok Saat Ini</label>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-extrabold text-slate-900">
                            <span x-text="formatQuantity(currentItem.stok)"></span>
                            <span class="text-slate-400" x-text="currentItem.satuan"></span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="catatan" rows="3" x-model="currentItem.catatan" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modal === 'restock' && currentItem" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Restock Stok</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form :action="inventoryAction(currentItem.id, 'restock')" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-slate-400">Barang</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900" x-text="currentItem.nama_barang"></p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Jumlah</label>
                    <div class="relative">
                        <input type="number" name="jumlah" min="0.01" step="0.01" required class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 pr-20 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-bold text-slate-400" x-text="currentItem.satuan"></span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: Restock dari supplier A" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modal === 'usage' && currentItem" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Catat Pemakaian Stok</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form :action="inventoryAction(currentItem.id, 'use')" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-slate-400">Barang</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900" x-text="currentItem.nama_barang"></p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Jumlah</label>
                    <div class="relative">
                        <input type="number" name="jumlah" min="0.01" step="0.01" required class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 pr-20 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-bold text-slate-400" x-text="currentItem.satuan"></span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Catatan</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: Dipakai untuk order #ABC123" class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modal === 'history' && currentItem" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-4xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Riwayat Stok: <span x-text="currentItem.nama_barang"></span></h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-8 py-8">
                <div class="overflow-hidden rounded-[24px] border border-slate-100">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-50 bg-slate-50/30">
                                <th class="px-6 py-5">Tanggal</th>
                                <th class="px-6 py-5">Tipe</th>
                                <th class="px-6 py-5">Jumlah</th>
                                <th class="px-6 py-5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-if="(currentItem.stock_movements || []).length === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm font-bold text-slate-400">Belum ada riwayat stok.</td>
                                </tr>
                            </template>
                            <template x-for="history in (currentItem.stock_movements || [])" :key="history.id">
                                <tr>
                                    <td class="px-6 py-5 text-sm font-bold text-slate-500" x-text="formatDate(history.created_at)"></td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-extrabold"
                                            :class="history.type === 'IN' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
                                            x-text="history.type === 'IN' ? 'MASUK' : 'KELUAR'"></span>
                                    </td>
                                    <td class="px-6 py-5 text-sm font-extrabold"
                                        :class="history.type === 'IN' ? 'text-emerald-600' : 'text-rose-600'">
                                        <span x-text="(history.type === 'IN' ? '+' : '-') + formatQuantity(history.quantity)"></span>
                                        <span class="font-bold text-slate-400" x-text="currentItem.satuan"></span>
                                    </td>
                                    <td class="px-6 py-5 text-sm italic text-slate-500" x-text="history.catatan || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-8 flex justify-end">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('inventoryPage', () => ({
            inventories: @json($inventories->items()),
            outlets: @json($outlets),
            inventoryBaseUrl: @js(url('/inventories')),
            modal: null,
            currentItem: null,

            findInventory(id) {
                return this.inventories.find(item => item.id === id) || null;
            },

            openCreateModal() {
                this.modal = 'create';
                this.currentItem = null;
            },

            openEditModal(item) {
                this.currentItem = JSON.parse(JSON.stringify(item));
                this.modal = 'edit';
            },

            openRestockModal(item) {
                this.currentItem = JSON.parse(JSON.stringify(item));
                this.modal = 'restock';
            },

            openUsageModal(item) {
                this.currentItem = JSON.parse(JSON.stringify(item));
                this.modal = 'usage';
            },

            openHistoryModal(item) {
                this.currentItem = JSON.parse(JSON.stringify(item));
                this.modal = 'history';
            },

            closeModal() {
                this.modal = null;
                this.currentItem = null;
            },

            inventoryAction(id, suffix = '') {
                return this.inventoryBaseUrl + '/' + id + (suffix ? '/' + suffix : '');
            },

            formatQuantity(value) {
                const number = Number(value || 0);
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: number % 1 === 0 ? 0 : 2,
                    maximumFractionDigits: 2,
                }).format(number);
            },

            formatDate(value) {
                return new Date(value).toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            },
        }));
    });
</script>
@endsection
