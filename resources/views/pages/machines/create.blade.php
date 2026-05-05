@extends('layouts.app')

@section('title', 'Add New Machine - Laundry Track')

@section('content')
<div class="mx-auto max-w-4xl pb-10">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">Add New Machine</h2>
            <p class="text-sm font-medium text-slate-400">Register a new washer or dryer unit</p>
        </div>
        <a href="{{ route('machines.index') }}" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
    </div>

    <form action="{{ route('machines.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Basic Info --}}
            <div class="rounded-[32px] bg-white p-8 shadow-sm ring-1 ring-slate-100 space-y-6">
                <h3 class="text-lg font-extrabold text-slate-900">Machine Information</h3>
                
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Machine Code</label>
                    <input type="text" name="machine_code" required placeholder="e.g. W-01, D-05" value="{{ old('machine_code') }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    @error('machine_code') <p class="text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Machine Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="machine_type" value="WASHER" class="peer hidden" checked>
                            <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-50 bg-slate-50 py-4 text-sm font-extrabold text-slate-400 transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="3" width="12" height="18" rx="2"></rect><circle cx="12" cy="14" r="3"></circle></svg>
                                Washer
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="machine_type" value="DRYER" class="peer hidden">
                            <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-50 bg-slate-50 py-4 text-sm font-extrabold text-slate-400 transition peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 7l7 5 7-5M5 17l7-5 7 5"></path></svg>
                                Dryer
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Brand</label>
                        <input type="text" name="brand" placeholder="e.g. LG, Samsung" value="{{ old('brand') }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Capacity (KG)</label>
                        <input type="number" name="capacity_kg" required placeholder="7" value="{{ old('capacity_kg') }}" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Initial Status</label>
                    <select name="status" class="block w-full rounded-2xl border-slate-100 bg-slate-50 py-3.5 px-4 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                        <option value="AVAILABLE">Available</option>
                        <option value="MAINTENANCE">Maintenance</option>
                        <option value="FAULTY">Faulty</option>
                    </select>
                </div>
            </div>

            {{-- Pricing & Durations --}}
            <div class="rounded-[32px] bg-white p-8 shadow-sm ring-1 ring-slate-100 space-y-6">
                <h3 class="text-lg font-extrabold text-slate-900">Program Pricing</h3>
                
                @foreach(['WASH' => 'Wash Program', 'DRY' => 'Dry Program', 'COMPLETE' => 'Complete Cycle'] as $key => $label)
                <div class="rounded-2xl border border-slate-50 bg-slate-50/50 p-4 space-y-4">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-primary-600">{{ $label }}</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-400 uppercase">Duration (Min)</label>
                            <input type="number" name="durations[{{ $key }}][duration_minutes]" value="{{ old('durations.'.$key.'.duration_minutes', $key == 'WASH' ? 30 : ($key == 'DRY' ? 45 : 90)) }}" class="block w-full rounded-xl border-slate-100 bg-white py-2.5 px-3 text-xs font-bold text-slate-900">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-400 uppercase">Price (Rp)</label>
                            <input type="number" name="durations[{{ $key }}][price]" value="{{ old('durations.'.$key.'.price', $key == 'COMPLETE' ? 50000 : 25000) }}" class="block w-full rounded-xl border-slate-100 bg-white py-2.5 px-3 text-xs font-bold text-slate-900">
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="pt-4">
                    <button type="submit" class="w-full rounded-2xl bg-primary-600 py-4 text-sm font-extrabold text-white shadow-xl shadow-primary-500/25 transition hover:bg-primary-700 active:scale-[0.98]">
                        Save Machine
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
