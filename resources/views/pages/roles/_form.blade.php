@csrf
@if(isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="space-y-6">
    <div class="space-y-2">
        <label for="name" class="text-sm font-bold text-slate-700">Nama Role</label>
        <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100" placeholder="Contoh: Supervisor">
        @error('name')
            <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="text-sm font-bold text-slate-700">Assign Permission</label>
            <p class="text-xs font-semibold text-slate-400">{{ count($permissions) }} permission tersedia</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($permissions as $permission)
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-brand-200 hover:bg-brand-50">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $selectedPermissions), true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>
                        <span class="block text-sm font-bold text-slate-800">{{ $permission->name }}</span>
                        <span class="block text-xs text-slate-400">{{ $permission->guard_name }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('permissions')
            <p class="text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            Batal
        </a>
    </div>
</div>
