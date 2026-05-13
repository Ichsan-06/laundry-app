@extends('layouts.app')

@section('title', 'Pengaturan - Laundry Coin')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola informasi outlet dan sistem')

@section('content')
<div class="max-w-4xl">
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 p-4 text-emerald-700 shadow-sm border border-emerald-100">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid gap-8">
        <!-- Outlet Information Card -->
        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-soft">
            <div class="border-b border-slate-50 bg-slate-50/50 px-8 py-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-lg shadow-primary-500/20">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Informasi Outlet</h2>
                        <p class="text-sm font-semibold text-slate-400">Update identitas dan alamat outlet Anda</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('settings.outlet.update') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label for="nama_outlet" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Nama Outlet</label>
                        <input type="text" id="nama_outlet" name="nama_outlet" value="{{ old('nama_outlet', $outlet->nama_outlet ?? '') }}" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                            placeholder="Contoh: Laundry Express Center">
                        @error('nama_outlet') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="telepon" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Nomor Telepon</label>
                        <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $outlet->telepon ?? '') }}" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                            placeholder="0812xxxxxx">
                        @error('telepon') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label for="alamat" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                            placeholder="Jl. Merdeka No. 123...">{{ old('alamat', $outlet->alamat ?? '') }}</textarea>
                        @error('alamat') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="kota" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Kota</label>
                        <input type="text" id="kota" name="kota" value="{{ old('kota', $outlet->kota ?? '') }}" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                            placeholder="Contoh: Jakarta Selatan">
                        @error('kota') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Status Outlet</label>
                        <div class="flex h-[54px] items-center">
                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-extrabold text-emerald-600">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif & Beroperasi
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 rounded-3xl border border-slate-100 bg-slate-50/70 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 7h20"></path>
                                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                <path d="M6 15h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Pengaturan QRIS WijayaPay</h3>
                            <p class="mt-1 text-sm font-semibold text-slate-400">Data ini dipakai saat kasir membuat transaksi QRIS untuk outlet yang sedang aktif.</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="wijayapay_merchant_code" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Merchant Code</label>
                            <input type="text" id="wijayapay_merchant_code" name="wijayapay_merchant_code" value="{{ old('wijayapay_merchant_code', $outlet->wijayapay_merchant_code ?? '') }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                                placeholder="Masukkan merchant code WijayaPay">
                            @error('wijayapay_merchant_code') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="wijayapay_api_key" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">API Key</label>
                            <input type="text" id="wijayapay_api_key" name="wijayapay_api_key" value="{{ old('wijayapay_api_key', $outlet->wijayapay_api_key ?? '') }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                                placeholder="Masukkan API key WijayaPay">
                            @error('wijayapay_api_key') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="wijayapay_create_url" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Create URL</label>
                            <input type="url" id="wijayapay_create_url" name="wijayapay_create_url" value="{{ old('wijayapay_create_url', $outlet->wijayapay_create_url ?? '') }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                                placeholder="https://wijayapay.com/api/transaction/create">
                            @error('wijayapay_create_url') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="wijayapay_status_url" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Status URL</label>
                            <input type="url" id="wijayapay_status_url" name="wijayapay_status_url" value="{{ old('wijayapay_status_url', $outlet->wijayapay_status_url ?? '') }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                                placeholder="https://wijayapay.com/api/get-status">
                            @error('wijayapay_status_url') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- <div class="space-y-2">
                            <label for="wijayapay_callback_url" class="text-[13px] font-extrabold uppercase tracking-wider text-slate-400">Callback URL</label>
                            <input type="url" id="wijayapay_callback_url" name="wijayapay_callback_url" value="{{ old('wijayapay_callback_url', $outlet->wijayapay_callback_url ?? '') }}"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-bold text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary-500/10"
                                placeholder="https://domain-anda.com/wijayapay/callback">
                            @error('wijayapay_callback_url') <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div> -->
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="flex items-center gap-3 rounded-2xl bg-primary-600 px-8 py-4 text-sm font-extrabold text-white shadow-lg shadow-primary-500/30 transition hover:bg-primary-700 hover:shadow-primary-500/40 active:scale-95">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- System Preferences (Placeholders for future) -->
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-soft">
            <h3 class="text-lg font-extrabold text-slate-900">Preferensi Sistem</h3>
            <p class="mt-1 text-sm font-semibold text-slate-400">Atur perilaku aplikasi lainnya</p>
            
            <div class="mt-8 space-y-6">
                <div class="flex items-center justify-between rounded-2xl border border-slate-50 bg-slate-50/30 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Mode Gelap (Coming Soon)</p>
                            <p class="text-xs font-semibold text-slate-400">Aktifkan antarmuka bertema gelap</p>
                        </div>
                    </div>
                    <div class="h-6 w-11 rounded-full bg-slate-200"></div>
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-slate-50 bg-slate-50/30 p-5 opacity-60">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Backup Data Otomatis</p>
                            <p class="text-xs font-semibold text-slate-400">Simpan salinan database setiap hari</p>
                        </div>
                    </div>
                    <div class="h-6 w-11 rounded-full bg-slate-200"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
