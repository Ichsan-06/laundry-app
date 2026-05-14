@extends('layouts.app')

@section('title', 'Transaction Detail - ' . $transaction->transaction_number)

@section('header')
<header class="sticky top-0 z-20 flex min-h-[84px] shrink-0 items-center justify-between gap-6 border-b border-slate-100 bg-white/95 px-4 py-4 backdrop-blur md:min-h-[108px] md:px-10">
    <div class="flex flex-1 items-center gap-4">
        <a href="{{ route('transactions.index') }}" class="group flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 transition hover:bg-primary-50 hover:text-primary-600">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </a>
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">{{ $transaction->transaction_number }}</h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @php
            $statusClasses = [
                'PENDING' => 'bg-amber-50 text-amber-600 ring-amber-100',
                'IN_PROGRESS' => 'bg-blue-50 text-blue-600 ring-blue-100',
                'READY' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
                'COMPLETED' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                'CANCELLED' => 'bg-rose-50 text-rose-600 ring-rose-100',
            ];
        @endphp
        <span class="inline-flex rounded-xl px-4 py-2 text-[11px] font-extrabold tracking-widest ring-1 ring-inset {{ $statusClasses[$transaction->status] ?? 'bg-slate-100 text-slate-500' }}">
            {{ str_replace('_', ' ', $transaction->status) }}
        </span>
        <a href="{{ route('kasir.receipt', $transaction->id) }}" target="_blank" class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 text-xs font-extrabold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Receipt
        </a>

        @if($transaction->member && $transaction->member->no_hp)
            @php
                $waMessage = "Halo *" . ($transaction->member->nama ?? 'Pelanggan') . "*,\n\n";
                $waMessage .= "Berikut adalah ringkasan transaksi Anda di *" . $transaction->outlet->nama_outlet . "*:\n\n";
                $waMessage .= "📌 *Detail Transaksi*\n";
                $waMessage .= "No. Transaksi: " . $transaction->transaction_number . "\n";
                $waMessage .= "Tanggal: " . $transaction->created_at->format('d/m/Y H:i') . "\n";
                $waMessage .= "Status: " . str_replace('_', ' ', $transaction->status) . "\n";
                $waMessage .= "--------------------------------\n";
                
                if($transaction->transaction_type === 'SELF_SERVICE') {
                    foreach($transaction->selfServiceDetails as $detail) {
                        $waMessage .= "• " . $detail->machine->machine_type . " (" . $detail->duration_minutes . " mnt): Rp " . number_format($detail->price, 0, ',', '.') . "\n";
                    }
                } else {
                    foreach($transaction->servicePackages as $pkg) {
                        $waMessage .= "• " . $pkg->nama_paket . " (" . $pkg->pivot->weight . "kg): Rp " . number_format($pkg->pivot->price * $pkg->pivot->weight, 0, ',', '.') . "\n";
                    }
                    foreach($transaction->addonOptions as $addon) {
                        $waMessage .= "• " . $addon->nama . ": Rp " . number_format($addon->pivot->price, 0, ',', '.') . "\n";
                    }
                }
                
                $waMessage .= "--------------------------------\n";
                $waMessage .= "💰 *TOTAL: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "*\n";
                $waMessage .= "--------------------------------\n\n";
                $waMessage .= "Terima kasih telah mempercayakan cucian Anda kepada kami! 🙏✨";
                
                $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $transaction->member->no_hp));
                $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waMessage);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-6 text-xs font-extrabold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-700">
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
<div class="mx-auto max-w-6xl space-y-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        
        {{-- Left Column: Order Content --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- Service Details --}}
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
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-widest">Detail Layanan</h3>
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
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $detail->machine_duration_id ? 'Paket Durasi Custom' : 'Default' }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($detail->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @else
                        {{-- Drop Off Packages --}}
                        @foreach($transaction->servicePackages as $pkg)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                                        <span class="text-[10px] font-extrabold text-slate-400">BERAT</span>
                                        <span class="text-sm font-black text-primary-600">{{ $pkg->pivot->weight }}<small class="text-[10px]">Kg</small></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">{{ $pkg->nama_paket }}</p>
                                        @if($pkg->pivot->note)
                                            <p class="text-[10px] font-bold text-rose-500 uppercase tracking-tighter">Note: {{ $pkg->pivot->note }}</p>
                                        @else
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Harga: Rp {{ number_format($pkg->pivot->price, 0, ',', '.') }}/kg</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($pkg->pivot->price * max($pkg->pivot->weight, 1), 0, ',', '.') }}</p>
                            </div>
                        @endforeach

                        {{-- Addons --}}
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

                        {{-- Item Inventory --}}
                        @if($transaction->items->count() > 0)
                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <p class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Daftar Item / Pakaian</p>
                                <div class="overflow-hidden rounded-2xl ring-1 ring-slate-100">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 font-extrabold text-slate-900">NAMA ITEM</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-900 text-center">QTY</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-900">CATATAN</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @foreach($transaction->items as $item)
                                                <tr>
                                                    <td class="px-4 py-3 font-bold text-slate-600">{{ $item->nama_item }}</td>
                                                    <td class="px-4 py-3 font-black text-slate-900 text-center">{{ $item->qty }}</td>
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

            {{-- Notes Section --}}
            <div class="rounded-[32px] bg-white p-8 shadow-soft ring-1 ring-slate-100">
                <h3 class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Catatan Transaksi</h3>
                <div class="rounded-2xl bg-slate-50 p-5 text-sm font-medium text-slate-600 italic">
                    {{ $transaction->notes ?: 'Tidak ada catatan untuk transaksi ini.' }}
                </div>
            </div>
        </div>

        {{-- Right Column: Information --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Customer Card --}}
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
                            <p class="mt-1 text-[11px] font-extrabold text-indigo-500 uppercase tracking-widest">Member Active</p>
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

            {{-- Payment Summary --}}
            <div class="rounded-[32px] bg-indigo-600 p-8 shadow-xl shadow-indigo-500/20 text-white">
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
                            <span class="text-sm font-extrabold text-indigo-100 uppercase tracking-widest">Total Bayar</span>
                            <span class="text-2xl font-black">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/20">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-indigo-100">Metode: <span class="text-white">{{ $transaction->payment_method }}</span></span>
                            <span class="font-bold text-indigo-100">Status: <span class="text-emerald-400 font-black">PAID</span></span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs">
                            <span class="font-bold text-indigo-100">Diterima:</span>
                            <span class="font-bold">Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-xs border-t border-white/10 pt-1">
                            <span class="font-bold text-indigo-100">Kembalian:</span>
                            <span class="font-black text-emerald-400">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metadata Card --}}
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
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @media print {
        header, .lg\:col-span-4, .sticky { display: none !important; }
        .mx-auto { max-width: 100% !important; margin: 0 !important; }
        .lg\:col-span-8 { width: 100% !important; }
        .rounded-\[32px\] { border-radius: 0 !important; box-shadow: none !important; ring: none !important; }
    }
</style>
@endsection
