@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Perbarui data user, role, password, dan status akun.')

@section('content')
    <div class="mx-auto max-w-3xl rounded-[32px] border border-white/70 bg-white p-6 shadow-soft sm:p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                    @error('nama')
                        <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                    @error('email')
                        <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Role</label>
                    <select name="role" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100">
                        <option value="">Pilih role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->display_role) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Password Baru</label>
                    <input type="password" name="password" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100" placeholder="Kosongkan jika tidak diubah">
                    @error('password')
                        <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100" placeholder="Ulangi password baru">
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $user->aktif)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm font-bold text-slate-700">Akun aktif</span>
            </label>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                Kosongkan field password jika Anda hanya ingin mengubah profil atau role user.
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">
                    Update User
                </button>
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
