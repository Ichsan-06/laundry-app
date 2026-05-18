@extends('layouts.app')

@section('title', 'Transaction Detail - ' . $transaction->transaction_number)

@php
    $statusClasses = [
        'PENDING' => 'bg-amber-50 text-amber-600 ring-amber-100',
        'IN_PROGRESS' => 'bg-blue-50 text-blue-600 ring-blue-100',
        'READY' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
        'COMPLETED' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        'CANCELLED' => 'bg-rose-50 text-rose-600 ring-rose-100',
    ];

    $statusLabels = [
        'PENDING' => 'Pending',
        'IN_PROGRESS' => 'Diproses',
        'READY' => 'Siap Diambil',
        'COMPLETED' => 'Selesai',
        'CANCELLED' => 'Dibatalkan',
    ];

    $isDropOff = $transaction->isDropOff();
    $processSteps = \App\Models\Transaction::DROP_OFF_PROCESS_STEPS;
    $processLabels = [
        'RECEIVED' => 'Diterima',
        'WASHED' => 'Dicuci',
        'DRIED' => 'Dikeringkan',
        'IRONED' => 'Disetrika',
        'READY' => 'Selesai',
        'PICKED_UP' => 'Diambil',
    ];
    $processButtonLabels = [
        'WASHED' => 'Tandai Sudah Dicuci',
        'DRIED' => 'Tandai Sudah Dikeringkan',
        'IRONED' => 'Tandai Sudah Disetrika',
        'READY' => 'Selesaikan Order',
        'PICKED_UP' => 'Tandai Diambil',
    ];
    $currentProcessStep = $isDropOff ? $transaction->currentProcessStep() : null;
    $currentProcessIndex = $isDropOff ? array_search($currentProcessStep, $processSteps, true) : false;
    $nextProcessStep = $isDropOff ? $transaction->nextProcessStep() : null;
    $waReadyMessage = $isDropOff ? $transaction->whatsappReadyMessage() : null;
    $waReadyUrl = $isDropOff ? $transaction->whatsappReadyUrl() : null;
@endphp

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 flex-col gap-4 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:flex-row md:items-center md:justify-between md:px-10">
    <div class="flex flex-1 items-center gap-4">
        <a href="{{ route('transactions.index') }}" class="group flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 transition hover:bg-primary-50 hover:text-primary-600">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </a>
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">{{ $transaction->transaction_number }}</h2>
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <span class="inline-flex rounded-xl px-4 py-2 text-[11px] font-extrabold tracking-widest ring-1 ring-inset {{ $statusClasses[$transaction->status] ?? 'bg-slate-100 text-slate-500' }}">
            {{ $statusLabels[$transaction->status] ?? str_replace('_', ' ', $transaction->status) }}
        </span>

        <a href="{{ route('kasir.receipt', $transaction->id) }}" target="_blank" class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 text-xs font-extrabold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Receipt
        </a>

        @if($isDropOff && $currentProcessStep === 'READY' && $waReadyUrl)
            <a href="{{ $waReadyUrl }}" target="_blank" class="flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-6 text-xs font-extrabold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-11.7 8.38 8.38 0 0 1 3.8.9L21 3z"></path>
                </svg>
                Kirim WA
            </a>
        @endif
    </div>
</header>
@endsection

@section('content')
<div x-data="{ readyModalOpen: false }" class="mx-auto max-w-6xl space-y-8">
    @if($isDropOff)
        <div class="rounded-[32px] bg-white p-0 shadow-soft ring-1 ring-slate-100">
            <div class="border-b border-slate-100 px-8 py-6">
                <h3 class="text-2xl font-extrabold text-slate-900">Timeline Status</h3>
            </div>
            <div class="px-6 py-8 md:px-8">
                <div class="grid grid-cols-2 gap-6 md:grid-cols-3 xl:grid-cols-6">
                    @foreach($processSteps as $index => $step)
                        @php
                            $isCompleted = $currentProcessIndex !== false && $index < $currentProcessIndex;
                            $isCurrent = $currentProcessStep === $step;
                            $isPending = ! $isCompleted && ! $isCurrent;
                        @endphp
                        <div class="flex flex-col items-center text-center">
                            <div class="@if($isCompleted) bg-teal-500 text-white ring-teal-100 @elseif($isCurrent) bg-white text-teal-600 ring-4 ring-teal-100 border-4 border-teal-500 @else bg-slate-50 text-slate-500 ring-1 ring-slate-200 @endif flex h-16 w-16 items-center justify-center rounded-full text-2xl font-black shadow-sm">
                                @if($isCompleted)
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <span>{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <p class="@if($isCurrent) text-teal-600 @elseif($isCompleted) text-slate-900 @else text-slate-500 @endif mt-4 text-xl font-extrabold">
                                {{ $processLabels[$step] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($transaction->status !== 'CANCELLED')
            <div class="rounded-[32px] bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-8 text-white shadow-soft">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.32em] text-cyan-200">Aksi Progress</p>
                        <h3 class="mt-3 text-2xl font-black">
                            {{ $processLabels[$currentProcessStep] ?? 'Status Tidak Tersedia' }}
                        </h3>
                        <p class="mt-2 max-w-2xl text-sm font-medium leading-7 text-slate-300">
                            Timeline ini hanya berlaku untuk order drop off. Status akan berjalan berurutan sampai selesai dan diambil pelanggan.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if($nextProcessStep && $nextProcessStep !== 'READY')
                            <form method="POST" action="{{ route('transactions.advance-process', $transaction) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="step" value="{{ $nextProcessStep }}">
                                <button type="submit" class="rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-slate-900 transition hover:bg-slate-100">
                                    {{ $processButtonLabels[$nextProcessStep] ?? 'Lanjutkan' }}
                                </button>
                            </form>
                        @endif

                        @if($nextProcessStep === 'READY')
                            <button type="button" @click="readyModalOpen = true" class="rounded-2xl bg-teal-500 px-6 py-3 text-sm font-extrabold text-white transition hover:bg-teal-600">
                                {{ $processButtonLabels[$nextProcessStep] }}
                            </button>
                        @endif

                        @if($currentProcessStep === 'READY' && $waReadyUrl)
                            <a href="{{ $waReadyUrl }}" target="_blank" class="rounded-2xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-extrabold text-white transition hover:bg-white/20">
                                Kirim ke WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div x-show="readyModalOpen" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" x-cloak>
            <div @click.away="readyModalOpen = false" class="w-full max-w-4xl overflow-hidden rounded-[32px] bg-white shadow-2xl ring-1 ring-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 px-8 py-7">
                    <h3 class="text-2xl font-extrabold text-slate-900">Kirim Notifikasi WhatsApp?</h3>
                    <button type="button" @click="readyModalOpen = false" class="text-slate-400 transition hover:text-slate-700">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-7 px-8 py-8">
                    <p class="text-2xl leading-[1.6] text-slate-500">
                        Order akan ditandai sebagai "Selesai". Kirim notifikasi ke pelanggan?
                    </p>

                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 px-8 py-7">
                        <p class="text-2xl font-extrabold text-slate-900">Pratinjau Pesan:</p>
                        <p class="mt-5 text-2xl italic leading-[1.5] text-slate-500">
                            "{{ $waReadyMessage ?? 'Nomor WhatsApp pelanggan belum tersedia.' }}"
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <form method="POST" action="{{ route('transactions.advance-process', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="step" value="READY">
                            <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white px-8 py-4 text-lg font-extrabold text-slate-900 transition hover:bg-slate-50 sm:w-auto">
                                Selesaikan Tanpa Notifikasi
                            </button>
                        </form>

                        @if($waReadyUrl)
                            <form method="POST" action="{{ route('transactions.advance-process', $transaction) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="step" value="READY">
                                <input type="hidden" name="notify" value="1">
                                <button type="submit" class="w-full rounded-2xl bg-teal-500 px-8 py-4 text-lg font-extrabold text-white transition hover:bg-teal-600 sm:w-auto">
                                    Kirim via WhatsApp
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <div class="rounded-[32px] bg-white p-8 shadow-soft ring-1 ring-slate-100">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                            <path d="M4 12c0 6.07 4.93 11 11 11 2.42 0 4.63-1.15 6-3"></path>
                            <path d="m2 7 12.65 11.63a2 2 0 0 0 2.7 0L22 7"></path>
                            <path d="M2 7h20"></path>
                            <path d="M14.5 2v5"></path>
                            <path d="M9.5 2v5"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-extrabold uppercase tracking-widest text-slate-900">Detail Layanan</h3>
                </div>

                <div class="space-y-4">
                    @if($transaction->transaction_type === 'SELF_SERVICE')
                        @foreach($transaction->selfServiceDetails as $detail)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                                        <span class="text-[10px] font-extrabold text-slate-400">MESIN</span>
                                        <span class="text-sm font-black text-primary-600">{{ $detail->machine->machine_code }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $detail->machine->machine_type }} - {{ $detail->duration_minutes }} Menit</p>
                                        <p class="text-[10px] font-bold uppercase tracking-tighter text-slate-400">{{ $detail->machine_duration_id ? 'Paket Durasi Custom' : 'Default' }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($detail->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @else
                        @foreach($transaction->servicePackages as $pkg)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                                        <span class="text-[10px] font-extrabold text-slate-400">SATUAN</span>
                                        <span class="text-sm font-black text-primary-600">{{ $pkg->pivot->weight }}<small class="text-[10px]">{{ $pkg->satuanSingkat() }}</small></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $pkg->nama_paket }}</p>
                                        @if($pkg->pivot->note)
                                            <p class="text-[10px] font-bold uppercase tracking-tighter text-rose-500">Note: {{ $pkg->pivot->note }}</p>
                                        @else
                                            <p class="text-[10px] font-bold uppercase tracking-tighter text-slate-400">Harga: Rp {{ number_format($pkg->pivot->price, 0, ',', '.') }}/{{ $pkg->satuanSingkat() }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($pkg->pivot->price * max($pkg->pivot->weight, 1), 0, ',', '.') }}</p>
                            </div>
                        @endforeach

                        @if($transaction->addonOptions->count() > 0)
                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <p class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tambahan (Add-ons)</p>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach($transaction->addonOptions as $addon)
                                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white p-3">
                                            <span class="text-xs font-bold text-slate-600">{{ $addon->nama }}</span>
                                            <span class="text-xs font-extrabold text-slate-900">Rp {{ number_format($addon->pivot->price, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($transaction->items->count() > 0)
                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <p class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Daftar Item / Pakaian</p>
                                <div class="overflow-hidden rounded-2xl ring-1 ring-slate-100">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 font-extrabold text-slate-900">NAMA ITEM</th>
                                                <th class="px-4 py-3 text-center font-extrabold text-slate-900">QTY</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-900">CATATAN</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @foreach($transaction->items as $item)
                                                <tr>
                                                    <td class="px-4 py-3 font-bold text-slate-600">{{ $item->nama_item }}</td>
                                                    <td class="px-4 py-3 text-center font-black text-slate-900">{{ $item->qty }}</td>
                                                    <td class="px-4 py-3 text-slate-400">{{ $item->note ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="rounded-[32px] bg-white p-8 shadow-soft ring-1 ring-slate-100">
                <h3 class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Catatan Transaksi</h3>
                <div class="rounded-2xl bg-slate-50 p-5 text-sm font-medium italic text-slate-600">
                    {{ $transaction->notes ?: 'Tidak ada catatan untuk transaksi ini.' }}
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-4">
            <div class="rounded-[32px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
                <h3 class="mb-6 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Informasi Pelanggan</h3>
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-xl font-black text-primary-600 shadow-sm ring-1 ring-primary-100">
                        {{ strtoupper(substr($transaction->member->nama ?? 'G', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="truncate text-base font-black text-slate-900">{{ $transaction->member->nama ?? 'Guest Customer' }}</h4>
                        @if($transaction->member)
                            <p class="text-xs font-bold text-slate-400">{{ $transaction->member->id_member }}</p>
                            <p class="mt-1 text-[11px] font-extrabold uppercase tracking-widest text-indigo-500">Member Active</p>
                        @else
                            <p class="text-xs font-bold text-slate-400">Non-Member Transaction</p>
                        @endif
                    </div>
                </div>
                @if($transaction->member)
                    <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-50 pt-6">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">No. HP</p>
                            <p class="text-xs font-bold text-slate-900">{{ $transaction->member->no_hp }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rounded-[32px] bg-indigo-600 p-8 text-white shadow-xl shadow-indigo-500/20">
                <h3 class="mb-6 text-[10px] font-extrabold uppercase tracking-widest text-indigo-200">Ringkasan Pembayaran</h3>
                <div class="space-y-4">
                    <div class="flex justify-between text-sm font-bold">
                        <span class="text-indigo-200">Subtotal</span>
                        <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->discount_amount > 0)
                        <div class="flex justify-between text-sm font-bold">
                            <span class="text-indigo-200">Diskon ({{ number_format($transaction->discount_percent, 0) }}%)</span>
                            <span class="text-rose-300">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($transaction->tax_amount > 0)
                        <div class="flex justify-between text-sm font-bold">
                            <span class="text-indigo-200">Pajak ({{ number_format($transaction->tax_percent, 0) }}%)</span>
                            <span>+ Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="my-4 border-t border-indigo-500 pt-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-extrabold uppercase tracking-widest text-indigo-100">Total Bayar</span>
                            <span class="text-2xl font-black">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/20">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-indigo-100">Metode: <span class="text-white">{{ $transaction->payment_method }}</span></span>
                            <span class="font-bold text-indigo-100">Status: <span class="font-black text-emerald-400">{{ strtoupper($transaction->payment_status ?? 'paid') }}</span></span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <span class="font-bold text-indigo-100">Diterima:</span>
                            <span class="font-bold">Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1 flex items-center justify-between border-t border-white/10 pt-1 text-xs">
                            <span class="font-bold text-indigo-100">Kembalian:</span>
                            <span class="font-black text-emerald-400">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Kasir</p>
                        <p class="text-xs font-extrabold text-slate-900">{{ $transaction->cashier->nama }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Outlet</p>
                        <p class="text-xs font-extrabold text-slate-900">{{ $transaction->outlet->nama_outlet }}</p>
                    </div>
                    @if($transaction->estimated_finish)
                        <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-500">Estimasi Selesai</p>
                            <p class="text-xs font-black text-indigo-600">{{ \Carbon\Carbon::parse($transaction->estimated_finish)->format('d M, H:i') }}</p>
                        </div>
                    @endif
                    @if($isDropOff)
                        <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-500">Progress Saat Ini</p>
                            <p class="text-xs font-black text-emerald-600">{{ $processLabels[$currentProcessStep] ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, .lg\:col-span-4, .sticky {
            display: none !important;
        }

        .mx-auto {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .lg\:col-span-8 {
            width: 100% !important;
        }

        .rounded-\[32px\] {
            border-radius: 0 !important;
            box-shadow: none !important;
            ring: none !important;
        }
    }
</style>
@endsection
