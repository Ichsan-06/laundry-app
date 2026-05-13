<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Laundry Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            100: '#d9efff',
                            500: '#0f9bd7',
                            700: '#096892',
                        },
                    },
                    boxShadow: {
                        card: '0 30px 80px rgba(16, 36, 63, 0.18)',
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,155,215,0.34),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(255,184,77,0.26),_transparent_30%),linear-gradient(135deg,_#071426_0%,_#0f2844_45%,_#103755_100%)] font-sans text-slate-800">
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-5 py-8 lg:px-10">
        <div class="grid w-full gap-8 overflow-hidden rounded-[36px] border border-white/10 bg-white/8 shadow-card backdrop-blur lg:grid-cols-[1.1fr_0.9fr]">
            <section class="hidden flex-col justify-between p-10 text-white lg:flex">
                <div>
                    <div class="inline-flex items-center gap-3 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                        Secure Laundry Operations
                    </div>
                    <h1 class="mt-8 max-w-lg text-5xl font-extrabold leading-tight">Masuk ke panel laundry dengan akses sesuai peran Anda.</h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-200">
                        Login manual Laravel dengan validasi jelas, redirect berbasis role, dan kontrol akses penuh menggunakan Spatie Permission.
                    </p>
                </div>
                <div class="grid gap-4 text-sm text-slate-200 md:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                        <p class="font-extrabold text-white">Role Aware</p>
                        <p class="mt-2 leading-6">Super Admin, Admin, dan User diarahkan ke area kerja yang tepat.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                        <p class="font-extrabold text-white">Permission Ready</p>
                        <p class="mt-2 leading-6">Menu, halaman, dan aksi sensitif tampil sesuai permission user.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                        <p class="font-extrabold text-white">Clean UI</p>
                        <p class="mt-2 leading-6">Blade dan Tailwind dipakai tanpa Breeze atau Jetstream.</p>
                    </div>
                </div>
            </section>

            <section class="bg-white px-6 py-8 sm:px-10 sm:py-10">
                <div class="mx-auto max-w-md">
                    <div class="mb-8">
                        <p class="text-sm font-bold uppercase tracking-[0.3em] text-brand-500">Laundry Control</p>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Login ke sistem</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan email dan password akun Anda untuk masuk ke dashboard atau modul kasir.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                            <p class="font-bold">Login gagal</p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-bold text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100" placeholder="admin@laundry.com">
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-bold text-slate-700">Password</label>
                            <input id="password" type="password" name="password" autocomplete="current-password" required class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-900 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100" placeholder="Masukkan password">
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-brand-500 to-brand-700 px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-cyan-900/20 transition hover:translate-y-[-1px]">
                            Masuk Sekarang
                        </button>
                    </form>

                    <div class="mt-4 text-center text-sm font-medium text-slate-500">
                        Belum punya akun owner?
                        <a href="{{ route('register.owner') }}" class="font-extrabold text-brand-700 hover:text-brand-500">Daftar free trial</a>
                    </div>

                    {{-- <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                        <p class="font-bold text-slate-800">Akun seed default</p>
                        <p class="mt-2">superadmin@laundry.com, admin@laundry.com, user@laundry.com</p>
                        <p class="mt-1">Password semua akun: password</p>
                    </div> --}}
                </div>
            </section>
        </div>
    </div>
</body>
</html>
