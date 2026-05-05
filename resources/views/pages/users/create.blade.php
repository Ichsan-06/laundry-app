@extends('layouts.app')

@section('title', 'Add New User - Laundry Track')

@section('content')
<div class="mx-auto max-w-2xl pb-10">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">Add New User</h2>
            <p class="text-sm font-medium text-slate-400">Register a new system operator</p>
        </div>
        <a href="{{ route('users.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
    </div>

    <div class="rounded-[32px] bg-white p-8 shadow-sm ring-1 ring-slate-100">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Full Name</label>
                <input type="text" name="nama" required placeholder="Enter full name" value="{{ old('nama') }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                @error('nama') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Email Address</label>
                <input type="email" name="email" required placeholder="user@laundry.com" value="{{ old('email') }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                @error('email') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">System Role</label>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role" value="KASIR" class="peer hidden" checked>
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-50 bg-slate-50 py-4 text-sm font-extrabold text-slate-400 transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                            Kasir
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="role" value="ADMIN" class="peer hidden">
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-50 bg-slate-50 py-4 text-sm font-extrabold text-slate-400 transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                            Admin
                        </div>
                    </label>
                </div>
                @error('role') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Password</label>
                    <input type="password" name="password" required placeholder="Min 8 characters" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    @error('password') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <div class="flex items-center gap-3 py-2">
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="aktif" value="1" class="peer sr-only" checked>
                    <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white focus:outline-none focus:ring-4 focus:ring-primary-300"></div>
                </label>
                <span class="text-sm font-bold text-slate-600">Active Account</span>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full rounded-2xl bg-primary-600 py-4 text-sm font-extrabold text-white shadow-xl shadow-primary-500/25 transition hover:bg-primary-700 active:scale-[0.98]">
                    Save User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
