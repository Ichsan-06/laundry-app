<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-6 font-['Plus_Jakarta_Sans'] text-white">
    <div class="w-full max-w-2xl rounded-[36px] border border-white/10 bg-white/5 p-8 text-center shadow-2xl backdrop-blur sm:p-12">
        <p class="text-sm font-bold uppercase tracking-[0.34em] text-cyan-300">403 Forbidden</p>
        <h1 class="mt-4 text-4xl font-extrabold tracking-tight sm:text-5xl">Anda tidak punya akses ke halaman ini.</h1>
        <p class="mt-4 text-base leading-7 text-slate-300">
            Permission atau role akun Anda belum mengizinkan aksi ini. Silakan kembali ke area yang tersedia atau hubungi administrator.
        </p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ auth()->check() ? route('home') : route('login') }}" class="rounded-2xl bg-cyan-500 px-6 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-cyan-400">
                Kembali
            </a>
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-2xl border border-white/20 px-6 py-3 text-sm font-extrabold text-white transition hover:bg-white/10">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</body>
</html>
