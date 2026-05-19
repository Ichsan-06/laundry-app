@extends('layouts.app')

@section('title', 'Pengeluaran - Laundry Track')

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
            <form action="{{ route('outcomes.index') }}" method="GET">
                @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                @if(request('outlet_id')) <input type="hidden" name="outlet_id" value="{{ request('outlet_id') }}"> @endif
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari deskripsi atau kategori..."
                    class="block w-full rounded-xl border-none bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
            </form>
        </div>
    </div>
</header>
@endsection

@section('content')
<div x-data="outcomePage" class="mx-auto max-w-[1400px] space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pengeluaran</h2>
            <p class="mt-1 text-sm font-semibold text-slate-400">Catat dan pantau biaya operasional per cabang.</p>
        </div>
        <button type="button" @click="openCreateModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            Catat Pengeluaran
        </button>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                        <path d="M7 10h10"></path>
                        <path d="M7 14h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Pengeluaran</p>
                <h3 class="mt-1 text-3xl font-extrabold text-rose-500">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                <p class="mt-1 text-sm font-semibold text-slate-400">Sesuai cabang & filter saat ini</p>
            </div>
        </div>

        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                        <path d="M7 10h10"></path>
                        <path d="M7 14h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Jumlah Transaksi</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['total_entries'] }} <span class="text-lg font-bold text-slate-400">entri</span></h3>
            </div>
        </div>

        <div class="group rounded-[24px] bg-white p-6 shadow-soft ring-1 ring-slate-100 transition duration-300 hover:shadow-xl">
            <div class="flex items-start justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-110">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                        <path d="M7 10h10"></path>
                        <path d="M7 14h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Cabang Aktif</p>
                <h3 class="mt-1 text-3xl font-extrabold text-slate-900">{{ $stats['active_outlet_name'] }}</h3>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                    Kategori: {{ request('kategori') && request('kategori') !== 'all' ? ($categoryOptions[request('kategori')] ?? 'Semua') : 'Semua' }}
                </button>
                <div x-show="open" @click.away="open = false" class="absolute left-0 z-30 mt-2 w-72 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                    <a href="{{ route('outcomes.index', array_merge(request()->all(), ['kategori' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Semua Kategori</a>
                    @foreach($categoryOptions as $value => $label)
                        <a href="{{ route('outcomes.index', array_merge(request()->all(), ['kategori' => $value])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-white hover:shadow-sm">
                        Cabang: {{ request('outlet_id') && request('outlet_id') !== 'all' ? ($outlets->firstWhere('id', request('outlet_id'))?->nama_outlet ?? 'Semua') : 'Semua' }}
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 z-30 mt-2 w-56 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100" x-cloak>
                        <a href="{{ route('outcomes.index', array_merge(request()->all(), ['outlet_id' => 'all'])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">Semua Cabang</a>
                        @foreach($outlets as $outlet)
                            <a href="{{ route('outcomes.index', array_merge(request()->all(), ['outlet_id' => $outlet->id])) }}" class="block rounded-lg px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600">{{ $outlet->nama_outlet }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(request()->anyFilled(['search', 'kategori', 'outlet_id']))
                <a href="{{ route('outcomes.index') }}" class="text-xs font-extrabold text-rose-500 hover:underline">Hapus Filter</a>
            @endif
        </div>
    </div>

    @if($outcomes->count() === 0)
        <div class="rounded-[32px] border border-dashed border-slate-200 bg-white px-8 py-16 text-center shadow-soft ring-1 ring-slate-100">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[28px] bg-slate-50 text-slate-400">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                    <path d="M7 10h10"></path>
                    <path d="M7 14h4"></path>
                </svg>
            </div>
            <h3 class="mt-6 text-3xl font-extrabold text-slate-900">Belum ada pengeluaran</h3>
            <p class="mt-3 text-lg font-medium text-slate-500">Mulai catat pengeluaran pertama untuk melihat ringkasan biaya.</p>
            <button type="button" @click="openCreateModal()" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Catat Pengeluaran
            </button>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] bg-white shadow-soft ring-1 ring-slate-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50 bg-slate-50/30">
                            <th class="px-8 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Tanggal</th>
                            <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Kategori</th>
                            <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Cabang</th>
                            <th class="px-6 py-5 text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Deskripsi</th>
                            <th class="px-6 py-5 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Jumlah</th>
                            <th class="px-8 py-5 text-right text-[11px] font-extrabold uppercase tracking-widest text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($outcomes as $outcome)
                            <tr class="group transition hover:bg-slate-50/50">
                                <td class="px-8 py-5 text-sm font-bold text-slate-600">{{ $outcome->tanggal->format('d M Y') }}</td>
                                <td class="px-6 py-5 text-sm font-extrabold text-slate-900">{{ $outcome->categoryLabel() }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-slate-500">{{ $outcome->outlet?->nama_outlet ?? '-' }}</td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-500">{{ $outcome->deskripsi }}</td>
                                <td class="px-6 py-5 text-right text-sm font-extrabold text-rose-500">Rp {{ number_format($outcome->jumlah, 0, ',', '.') }}</td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="openEditModal(findOutcome('{{ $outcome->id }}'))" class="rounded-xl bg-slate-50 p-2.5 text-slate-400 transition hover:bg-primary-50 hover:text-primary-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('outcomes.destroy', $outcome) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $outcomes->links() }}
            </div>
        </div>
    @endif

    <div x-show="modal === 'create'" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Catat Pengeluaran Baru</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('outcomes.store') }}" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                @include('pages.outcomes.partials.form-fields', ['mode' => 'create'])
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="modal === 'edit' && currentOutcome" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" x-cloak>
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-8 py-6">
                <h3 class="text-xl font-extrabold text-slate-900">Edit Pengeluaran</h3>
                <button type="button" @click="closeModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form :action="outcomeAction(currentOutcome.id)" method="POST" class="space-y-5 px-8 py-8">
                @csrf
                @method('PUT')
                @include('pages.outcomes.partials.form-fields', ['mode' => 'edit'])
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-xl border-2 border-slate-100 px-6 py-3 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('outcomePage', () => ({
            outcomes: @json($outcomes->items()),
            modal: null,
            currentOutcome: null,

            findOutcome(id) {
                return this.outcomes.find(item => item.id === id) || null;
            },

            openCreateModal() {
                this.modal = 'create';
                this.currentOutcome = null;
            },

            openEditModal(item) {
                this.currentOutcome = JSON.parse(JSON.stringify(item));
                this.modal = 'edit';
            },

            closeModal() {
                this.modal = null;
                this.currentOutcome = null;
            },

            outcomeAction(id) {
                return @js(url('/outcomes')) + '/' + id;
            },
        }));
    });
</script>
@endsection
