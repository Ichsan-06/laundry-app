<div class="space-y-2">
    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Kategori</label>
    <select
        name="kategori"
        @if(($mode ?? 'create') === 'edit') x-model="currentOutcome.kategori" @endif
        class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"
    >
        @foreach($categoryOptions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

<div class="space-y-2">
    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Tanggal</label>
    <input
        type="date"
        name="tanggal"
        @if(($mode ?? 'create') === 'edit')
            x-model="currentOutcome.tanggal"
        @else
            value="{{ now()->format('Y-m-d') }}"
        @endif
        class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"
    >
</div>

<div class="space-y-2">
    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Cabang</label>
    <select
        name="outlet_id"
        @if(($mode ?? 'create') === 'edit') x-model="currentOutcome.outlet_id" @endif
        class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"
    >
        @foreach($outlets as $outlet)
            <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
        @endforeach
    </select>
</div>

<div class="space-y-2">
    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Deskripsi</label>
    <textarea
        name="deskripsi"
        rows="4"
        placeholder="Contoh: Beli deterjen 5kg"
        @if(($mode ?? 'create') === 'edit') x-model="currentOutcome.deskripsi" @endif
        class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"
    >{{ ($mode ?? 'create') === 'create' ? '' : null }}</textarea>
</div>

<div class="space-y-2">
    <label class="text-sm font-extrabold uppercase tracking-widest text-slate-400">Jumlah (Rp)</label>
    <input
        type="number"
        step="0.01"
        min="0"
        name="jumlah"
        value="{{ ($mode ?? 'create') === 'create' ? 0 : '' }}"
        @if(($mode ?? 'create') === 'edit') x-model="currentOutcome.jumlah" @endif
        class="block w-full rounded-2xl border-slate-100 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"
    >
</div>
