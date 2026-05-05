@extends('layouts.app')

@section('title', 'Edit Service Package - Laundry Track')

@section('content')
<div class="mx-auto max-w-2xl pb-10">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">Edit Service Package</h2>
            <p class="text-sm font-medium text-slate-400">Update package pricing and details</p>
        </div>
        <a href="{{ route('services.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
    </div>

    <div class="rounded-[32px] bg-white p-8 shadow-sm ring-1 ring-slate-100">
        <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-2">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Package Name</label>
                <input type="text" name="nama_paket" required value="{{ old('nama_paket', $service->nama_paket) }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                @error('nama_paket') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Description (Optional)</label>
                <textarea name="deskripsi" rows="3" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">{{ old('deskripsi', $service->deskripsi) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Price per KG (Rp)</label>
                    <input type="number" name="harga_per_kg" required value="{{ old('harga_per_kg', $service->harga_per_kg) }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    @error('harga_per_kg') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Min. Weight (KG)</label>
                    <input type="number" step="0.1" name="berat_minimal" required value="{{ old('berat_minimal', $service->berat_minimal) }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    @error('berat_minimal') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 py-2">
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="aktif" value="1" class="peer sr-only" {{ $service->aktif ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white focus:outline-none focus:ring-4 focus:ring-primary-300"></div>
                </label>
                <span class="text-sm font-bold text-slate-600">Active Package</span>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full rounded-2xl bg-primary-600 py-4 text-sm font-extrabold text-white shadow-xl shadow-primary-500/25 transition hover:bg-primary-700 active:scale-[0.98]">
                    Update Service Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
