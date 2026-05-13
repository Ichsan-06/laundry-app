@csrf
@if(isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-bold text-slate-700">Nama Outlet</label>
            <input type="text" name="nama_outlet" value="{{ old('nama_outlet', $outlet->nama_outlet) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
            @error('nama_outlet') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-bold text-slate-700">Alamat</label>
            <textarea name="alamat" rows="3" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('alamat', $outlet->alamat) }}</textarea>
            @error('alamat') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $outlet->telepon) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
            @error('telepon') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Kota</label>
            <input type="text" name="kota" value="{{ old('kota', $outlet->kota) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
            @error('kota') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5">
        <div>
            <p class="text-sm font-extrabold text-slate-900">Konfigurasi QRIS WijayaPay</p>
            <p class="mt-1 text-sm text-slate-500">Data ini akan dipakai oleh halaman kasir saat membuat transaksi QRIS untuk outlet ini.</p>
        </div>

        <div class="mt-5 grid gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700">Merchant Code</label>
                <input type="text" name="wijayapay_merchant_code" value="{{ old('wijayapay_merchant_code', $outlet->wijayapay_merchant_code) }}" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-100">
                @error('wijayapay_merchant_code') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700">API Key</label>
                <input type="text" name="wijayapay_api_key" value="{{ old('wijayapay_api_key', $outlet->wijayapay_api_key) }}" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-100">
                @error('wijayapay_api_key') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-bold text-slate-700">Create URL</label>
                <input type="url" name="wijayapay_create_url" value="{{ old('wijayapay_create_url', $outlet->wijayapay_create_url) }}" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-100">
                @error('wijayapay_create_url') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700">Status URL</label>
                <input type="url" name="wijayapay_status_url" value="{{ old('wijayapay_status_url', $outlet->wijayapay_status_url) }}" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-100">
                @error('wijayapay_status_url') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
            <!-- <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700">Callback URL</label>
                <input type="url" name="wijayapay_callback_url" value="{{ old('wijayapay_callback_url', $outlet->wijayapay_callback_url) }}" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-100">
                @error('wijayapay_callback_url') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div> -->
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $outlet->aktif ?? true)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
        <span class="text-sm font-bold text-slate-700">Outlet aktif</span>
    </label>
    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">{{ $submitLabel }}</button>
        <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</a>
    </div>
</div>
