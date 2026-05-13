<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Owner Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,155,215,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(244,161,0,0.16),_transparent_30%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] font-['Plus_Jakarta_Sans'] text-slate-800">
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid w-full gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-[36px] bg-slate-950 px-8 py-10 text-white shadow-2xl sm:px-10">
                <p class="text-sm font-bold uppercase tracking-[0.34em] text-cyan-300">Laundry SaaS</p>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight">Buka sistem laundry Anda sendiri dalam satu kali registrasi.</h1>
                <p class="mt-5 max-w-xl text-base font-medium text-slate-300">Setelah daftar, sistem langsung membuat tenant, outlet pertama, akun owner, dan free trial 14 hari dengan fitur utama aktif.</p>
                <div class="mt-10 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-2xl font-extrabold text-cyan-300">14 Hari</p>
                        <p class="mt-2 text-sm text-slate-300">Free trial aktif otomatis</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-2xl font-extrabold text-cyan-300">Multi Outlet</p>
                        <p class="mt-2 text-sm text-slate-300">Siap scale sesuai paket</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-2xl font-extrabold text-cyan-300">RBAC</p>
                        <p class="mt-2 text-sm text-slate-300">Role staff + permission plan</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[36px] border border-white/80 bg-white/95 p-6 shadow-xl backdrop-blur sm:p-8">
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-slate-400">Owner Registration</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Daftarkan Laundry Anda</h2>
                    <p class="mt-2 text-sm font-medium text-slate-500">Isi data owner dan outlet pertama untuk mulai memakai sistem.</p>
                </div>

                <form action="{{ route('register.owner.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-slate-700">Nama Owner</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('owner_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Nama Tenant</label>
                            <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('tenant_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Nama Outlet Pertama</label>
                            <input type="text" name="outlet_name" value="{{ old('outlet_name') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('outlet_name') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-slate-700">Email Login</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('email') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Telepon</label>
                            <input type="text" name="telepon" value="{{ old('telepon') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('telepon') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Kota</label>
                            <input type="text" name="kota" value="{{ old('kota') }}" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('kota') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-slate-700">Alamat Outlet</label>
                            <textarea name="alamat" rows="3" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">{{ old('alamat') }}</textarea>
                            @error('alamat') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Password</label>
                            <input type="password" name="password" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                            @error('password') <p class="text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                        </div>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-slate-800">
                        Buat Tenant & Mulai Trial
                    </button>
                    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 px-5 py-3.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Sudah punya akun? Login
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
