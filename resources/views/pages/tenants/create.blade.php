@extends('layouts.app')

@section('title', 'Tambah Tenant Owner')
@section('page-title', 'Tambah Tenant Owner')
@section('page-subtitle', 'Buat tenant laundry baru dari panel Super Admin dengan owner dan outlet pertama.')

@section('content')
    <div class="mx-auto max-w-4xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('tenants.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Nama Owner</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('owner_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Nama Tenant</label>
                    <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('tenant_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Outlet Pertama</label>
                    <input type="text" name="outlet_name" value="{{ old('outlet_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('outlet_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Email Owner</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('email') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('telepon') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('kota') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Alamat</label>
                    <textarea name="alamat" rows="3" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Password</label>
                    <input type="password" name="password" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('password') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">Simpan Tenant</button>
                <a href="{{ route('tenants.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
