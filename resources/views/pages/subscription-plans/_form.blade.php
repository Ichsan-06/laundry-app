@csrf
@if(isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Nama Plan</label>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
            @error('name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $plan->slug) }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
            @error('slug') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-bold text-slate-700">Deskripsi</label>
            <textarea name="description" rows="3" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('description', $plan->description) }}</textarea>
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Max Outlet</label>
            <input type="number" min="1" name="max_outlets" value="{{ old('max_outlets', $plan->max_outlets) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">Max Staff</label>
            <input type="number" min="1" name="max_staff" value="{{ old('max_staff', $plan->max_staff) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input type="checkbox" name="is_custom_permission" value="1" @checked(old('is_custom_permission', $plan->is_custom_permission)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-bold text-slate-700">Custom permission plan</span>
        </label>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-bold text-slate-700">Plan aktif</span>
        </label>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="text-sm font-bold text-slate-700">Permission Plan</label>
            <p class="text-xs font-semibold text-slate-400">{{ count($permissions) }} permission tersedia</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($permissions as $permission)
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-brand-200 hover:bg-brand-50">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $selectedPermissions), true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-bold text-slate-800">{{ $permission->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-brand-700">{{ $submitLabel }}</button>
        <a href="{{ route('subscription-plans.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</a>
    </div>
</div>
