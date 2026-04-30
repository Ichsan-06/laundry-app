@extends('layouts.app')

@section('title', 'Kasir - Laundry Coin')
@section('page-title', 'Kasir')
@section('page-subtitle', 'Buat Transaksi Baru')

@section('content')
<div class="mx-auto grid min-h-full w-full max-w-[1680px] grid-cols-1 gap-4 sm:gap-5 xl:grid-cols-[340px_minmax(520px,1fr)] 2xl:grid-cols-[380px_minmax(560px,1fr)_430px]">
    <div class="space-y-5">
        <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">1. Pilih Jenis Layanan</h2>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                <button class="min-h-[126px] rounded-lg border-2 border-primary-300 bg-primary-50/70 p-4 text-left shadow-[0_0_0_3px_rgba(109,85,232,0.08)] sm:min-h-[146px]">
                    <span class="flex h-11 w-11 items-center justify-center rounded-md text-primary-600">
                        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                            <path d="M8 7h8"></path>
                            <circle cx="12" cy="14" r="4"></circle>
                        </svg>
                    </span>
                    <span class="mt-4 block text-base font-extrabold text-primary-600">Self Service</span>
                    <span class="mt-2 block text-xs font-semibold text-slate-400">Pelanggan cuci sendiri</span>
                </button>

                <button class="min-h-[126px] rounded-lg border border-slate-100 bg-white p-4 text-left transition hover:border-primary-200 hover:bg-primary-50/30 sm:min-h-[146px]">
                    <span class="flex h-11 w-11 items-center justify-center rounded-md text-slate-500">
                        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M5 11h14l-1.5 9h-11z"></path>
                            <path d="M9 11V8a3 3 0 0 1 6 0v3"></path>
                            <path d="M8 15h8"></path>
                        </svg>
                    </span>
                    <span class="mt-4 block text-base font-extrabold text-slate-700">Drop Off</span>
                    <span class="mt-2 block text-xs font-semibold text-slate-400">Karyawan yang mencuci</span>
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">2. Data Pelanggan</h2>

            <div class="mt-5">
                <p class="text-sm font-extrabold text-slate-600">Tipe Pelanggan</p>
                <div class="mt-4 flex flex-wrap items-center gap-5 sm:gap-9">
                    <label class="flex items-center gap-3 text-sm font-bold text-slate-700">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-primary-600">
                            <span class="h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                        </span>
                        Member
                    </label>
                    <label class="flex items-center gap-3 text-sm font-bold text-slate-500">
                        <span class="h-5 w-5 rounded-full border-2 border-slate-200"></span>
                        Non Member
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label class="text-sm font-extrabold text-slate-600">Cari Member</label>
                <div class="mt-3 grid grid-cols-[1fr_48px_48px] gap-2">
                    <div class="flex min-w-0 items-center rounded-lg border border-slate-100 bg-white px-4 py-3 text-xs font-semibold leading-5 text-slate-400 shadow-sm sm:text-sm">
                        Masukkan nama / no HP / ID Member
                    </div>
                    <button class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-primary-100 bg-primary-50 text-primary-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </button>
                    <button class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-600 text-white shadow-lg shadow-primary-500/25">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-4 rounded-lg border border-primary-100 bg-primary-50/70 px-4 py-4 min-[420px]:flex-row min-[420px]:items-center">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-extrabold text-white">AN</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-extrabold text-slate-800">Anfi Nugroho</p>
                    <p class="mt-1 text-xs font-bold text-slate-400">ID Member: <span class="text-primary-600">MBR-000123</span></p>
                </div>
                <div class="text-left min-[420px]:text-right">
                    <p class="text-xs font-bold text-slate-400">Saldo</p>
                    <p class="mt-1 text-sm font-extrabold text-emerald-500">Rp 250.000</p>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">3. Pilih Mesin</h2>
            <p class="mt-2 text-xs font-bold text-slate-400">Pilih mesin yang akan digunakan</p>

            <div class="mt-7">
                <p class="text-sm font-extrabold text-slate-600">Tipe Mesin</p>
                <div class="mt-3 grid grid-cols-1 gap-3 min-[420px]:grid-cols-3">
                    <button class="rounded-lg border-2 border-primary-300 bg-primary-50 py-3 text-sm font-extrabold text-primary-600 shadow-[0_0_0_3px_rgba(109,85,232,0.08)]">Semua</button>
                    <button class="rounded-lg border border-slate-100 bg-white py-3 text-sm font-extrabold text-slate-500">Washer</button>
                    <button class="rounded-lg border border-slate-100 bg-white py-3 text-sm font-extrabold text-slate-500">Dryer</button>
                </div>
            </div>

            <div class="mt-7">
                <p class="text-sm font-extrabold text-slate-600">Durasi</p>
                <div class="mt-3 grid grid-cols-1 gap-3 min-[420px]:grid-cols-3">
                    <button class="rounded-lg border border-slate-100 bg-white py-3 text-sm font-extrabold text-slate-500 shadow-sm">Normal</button>
                    <button class="rounded-lg border border-slate-100 bg-white py-3 text-sm font-extrabold text-slate-500 shadow-sm">Cepat</button>
                    <button class="rounded-lg border border-slate-100 bg-white py-3 text-sm font-extrabold text-slate-500 shadow-sm">Ekstra Cepat</button>
                </div>
            </div>
        </section>
    </div>

    <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5 md:p-7">
        <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-lg font-extrabold tracking-tight text-slate-900">4. Pilih Mesin</h2>
                <p class="mt-3 text-sm font-bold text-slate-400">Pilih mesin yang tersedia</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs font-extrabold text-slate-500">
                <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
                <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>Digunakan</span>
                <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Maintenance</span>
            </div>
        </div>

        <div class="mt-7 grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 sm:gap-4 lg:grid-cols-4 xl:grid-cols-2 2xl:grid-cols-4">
            <button class="min-h-[188px] rounded-lg border-2 border-primary-500 bg-white p-4 text-center shadow-[0_0_0_4px_rgba(109,85,232,0.08)] sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                        <circle cx="28" cy="31" r="3" fill="#64748b"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">W-01</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Washer 11kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 25.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-emerald-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">W-02</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Washer 11kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 25.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-emerald-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">W-03</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Washer 14kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 30.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-amber-500"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>Digunakan</span>
                <span class="mt-2 block text-xs font-extrabold text-slate-500">Sisa 18 menit</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">W-04</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Washer 14kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 30.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-emerald-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">D-01</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Dryer 11kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 25.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-emerald-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">D-02</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Dryer 11kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 25.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-emerald-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Tersedia</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">D-03</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Dryer 14kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 30.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-amber-500"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>Digunakan</span>
                <span class="mt-2 block text-xs font-extrabold text-slate-500">Sisa 6 menit</span>
            </button>

            <button class="min-h-[188px] rounded-lg border border-slate-100 bg-white p-4 text-center shadow-sm sm:min-h-[220px] sm:p-5">
                <span class="mx-auto flex h-12 w-12 items-center justify-center text-slate-500 sm:h-16 sm:w-16">
                    <svg class="h-12 w-12 sm:h-16 sm:w-16" viewBox="0 0 64 64" fill="none">
                        <rect x="12" y="8" width="40" height="48" rx="3" fill="#e5e7eb" stroke="#9ca3af" stroke-width="2"/>
                        <path d="M17 15h16M41 15h4M47 15h2" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="32" cy="35" r="14" fill="#cbd5e1" stroke="#9ca3af" stroke-width="2"/>
                        <circle cx="32" cy="35" r="9" fill="#1f2937"/>
                    </svg>
                </span>
                <span class="mt-3 block text-lg font-extrabold text-slate-900 sm:mt-5 sm:text-xl">D-04</span>
                <span class="mt-2 block text-sm font-bold text-slate-400">Dryer 14kg</span>
                <span class="mt-2 block text-sm font-extrabold text-slate-600">Rp 30.000 / 30m</span>
                <span class="mt-5 flex items-center justify-center gap-2 text-sm font-extrabold text-rose-500"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Maintenance</span>
            </button>
        </div>

        <div class="mt-8 grid gap-4 rounded-lg border-2 border-primary-200 bg-primary-50/70 p-4 sm:mt-12 sm:p-5 md:grid-cols-[1fr_120px_120px] xl:grid-cols-1 2xl:grid-cols-[1fr_130px_130px_140px] 2xl:items-center">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md text-primary-600">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                        <path d="M8 7h8"></path>
                        <circle cx="12" cy="14" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400">Mesin terpilih</p>
                    <p class="mt-1 text-base font-extrabold text-slate-900">W-01</p>
                    <p class="text-xs font-bold text-slate-500">Washer 11kg</p>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400">Durasi</p>
                <p class="mt-2 text-base font-extrabold text-slate-900">30 Menit</p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400">Harga</p>
                <p class="mt-2 text-base font-extrabold text-slate-900">Rp 25.000</p>
            </div>
            <button class="rounded-lg border-2 border-primary-300 bg-white px-4 py-3 text-sm font-extrabold text-primary-600 md:col-span-3 xl:col-span-1 2xl:col-span-1">Ubah Pilihan</button>
        </div>
    </section>

    <div class="space-y-5 xl:col-span-2 2xl:col-span-1">
        <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">5. Ringkasan Pesanan</h2>

            <div class="mt-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <p class="text-sm font-extrabold text-slate-500">Jenis Layanan</p>
                    <span class="rounded-md border border-primary-200 bg-primary-50 px-3 py-1.5 text-sm font-extrabold text-primary-600">Self Service</span>
                </div>
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-sm font-extrabold text-slate-500">Mesin</p>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-extrabold text-slate-700">W-01 - Washer 11kg</p>
                        <p class="text-sm font-extrabold text-slate-700">30 Menit</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-5">
                    <p class="text-sm font-extrabold text-slate-500">Pelanggan</p>
                    <p class="text-sm font-extrabold text-primary-600">Anfi Nugroho (MBR-000123)</p>
                </div>
                <div class="space-y-4 border-b border-slate-100 pb-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-500">Subtotal</p>
                        <p class="text-base font-extrabold text-slate-700">Rp 25.000</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-500">Diskon Member</p>
                        <p class="text-base font-extrabold text-emerald-500">- Rp 0</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <p class="text-base font-extrabold text-slate-700">Total Bayar</p>
                    <p class="text-2xl font-extrabold tracking-tight text-primary-600 sm:text-3xl">Rp 25.000</p>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-100 bg-white p-4 shadow-card sm:p-5">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">6. Pembayaran</h2>

            <div class="mt-6">
                <p class="text-sm font-extrabold text-slate-500">Metode Pembayaran</p>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <button class="flex min-h-[86px] flex-col items-center justify-center gap-2 rounded-lg border border-slate-100 bg-white px-2 text-center text-xs font-extrabold text-slate-500 shadow-sm sm:text-sm">
                        <svg class="h-8 w-8 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M6 9v6M18 9v6"></path>
                        </svg>
                        Tunai
                    </button>
                    <button class="flex min-h-[86px] flex-col items-center justify-center gap-2 rounded-lg border-2 border-primary-300 bg-primary-50 px-2 text-center text-xs font-extrabold text-primary-600 shadow-[0_0_0_3px_rgba(109,85,232,0.08)] sm:text-sm">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="7" width="18" height="14" rx="2"></rect>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M3 12h18"></path>
                        </svg>
                        Saldo Member
                    </button>
                    <button class="flex min-h-[86px] flex-col items-center justify-center gap-2 rounded-lg border border-slate-100 bg-white px-2 text-center text-xs font-extrabold text-slate-500 shadow-sm sm:text-sm">
                        <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            <path d="M14 14h3v3h-3zM18 18h3v3h-3zM18 14h3M14 18v3"></path>
                        </svg>
                        QRIS
                    </button>
                    <button class="col-span-2 flex items-center gap-3 rounded-lg border border-slate-100 bg-white px-5 py-4 text-sm font-extrabold text-slate-500 shadow-sm sm:col-span-2">
                        <svg class="h-6 w-6 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="M3 10h18M7 15h4"></path>
                        </svg>
                        EDC / Card
                    </button>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center gap-3 rounded-lg border border-slate-100 bg-white p-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-extrabold text-slate-600">Saldo Member</p>
                </div>
                <p class="text-sm font-extrabold text-emerald-500">Rp 250.000</p>
                <button class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-slate-100 text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                        <path d="M3 21v-5h5M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                        <path d="M21 3v5h-5"></path>
                    </svg>
                </button>
            </div>

            <button class="mt-5 flex w-full items-center justify-center gap-3 rounded-lg bg-primary-600 px-5 py-4 text-base font-extrabold text-white shadow-lg shadow-primary-500/25">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <path d="m20 6-11 11-5-5"></path>
                </svg>
                Proses Pembayaran
            </button>
            <p class="mt-4 text-center text-xs font-bold text-slate-400">Mesin akan aktif setelah pembayaran berhasil</p>
        </section>
    </div>
</div>
@endsection
