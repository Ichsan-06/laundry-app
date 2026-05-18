<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WashKita - Aplikasi Manajemen Laundry Modern</title>
    <meta name="description" content="WashKita adalah aplikasi manajemen laundry modern untuk membantu transaksi, pelanggan, laporan, dan operasional laundry lebih mudah.">
    <meta name="keywords" content="aplikasi laundry, aplikasi manajemen laundry, aplikasi kasir laundry, software laundry, aplikasi laundry kiloan, aplikasi POS laundry, manajemen usaha laundry, aplikasi laundry online, aplikasi laundry berbasis web, aplikasi laundry terbaik, sistem laundry digital">
    <link rel="canonical" href="https://washkita.com/aplikasi-manajemen-laundry" />
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    
    <meta property="og:title" content="WashKita - Aplikasi Manajemen Laundry Modern">
    <meta property="og:description" content="WashKita adalah aplikasi manajemen laundry modern untuk membantu transaksi, pelanggan, laporan, dan operasional laundry lebih mudah.">
    <meta property="og:url" content="https://washkita.com/aplikasi-manajemen-laundry">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta name="theme-color" content="#00B4D8">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00B4D8;
            --secondary: #0F172A;
            --background: #F1F5F9;
            --text: #72787B;
            --white: #FFFFFF;
            --border: rgba(15, 23, 42, 0.08);
            --shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            --shadow-soft: 0 12px 30px rgba(15, 23, 42, 0.06);
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 16px;
            --container: 1180px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: "Hanken Grotesk", sans-serif;
            color: var(--secondary);
            background: var(--background);
        }

        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        .container {
            width: min(var(--container), calc(100% - 32px));
            margin: 0 auto;
        }

        .section {
            padding: 72px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(0, 180, 216, 0.12);
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .section-title {
            margin: 16px 0 12px;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.06;
            font-weight: 800;
            color: var(--secondary);
        }

        .section-copy {
            margin: 0;
            max-width: 640px;
            font-size: 18px;
            line-height: 1.7;
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 800;
            transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease, color 0.22s ease;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 16px 30px rgba(0, 180, 216, 0.26);
        }

        .btn-secondary {
            background: transparent;
            color: var(--secondary);
            border: 1px solid rgba(15, 23, 42, 0.12);
        }

        .btn-dark {
            background: var(--secondary);
            color: var(--white);
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 84px;
            gap: 16px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 24px;
            color: var(--secondary);
        }

        .brand-mark {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .nav-links {
            display: none;
            align-items: center;
            gap: 28px;
            font-weight: 700;
            color: var(--text);
        }

        .hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 1px 1px, rgba(15, 23, 42, 0.08) 1px, transparent 0);
            background-size: 24px 24px;
        }

        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(10px);
            opacity: 0.5;
            pointer-events: none;
        }

        .hero::before {
            width: 280px;
            height: 280px;
            right: -90px;
            top: 80px;
            background: rgba(0, 180, 216, 0.18);
        }

        .hero::after {
            width: 240px;
            height: 240px;
            left: -100px;
            bottom: 20px;
            background: rgba(15, 23, 42, 0.08);
        }

        .hero-grid {
            display: grid;
            gap: 40px;
            align-items: center;
            padding: 60px 0 48px;
        }

        .hero h1 {
            margin: 18px 0 16px;
            font-size: clamp(40px, 6vw, 68px);
            line-height: 0.97;
            letter-spacing: -0.03em;
            font-weight: 800;
        }

        .hero p {
            margin: 0;
            max-width: 620px;
            font-size: 18px;
            line-height: 1.8;
            color: var(--text);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .hero-note {
            margin-top: 20px;
            font-size: 14px;
            color: var(--text);
            font-weight: 600;
        }

        .mockup-shell {
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 32px;
            padding: 18px;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.12);
        }

        .mockup-shell::before {
            content: "";
            position: absolute;
            inset: auto 18px -18px 18px;
            height: 40px;
            background: rgba(15, 23, 42, 0.08);
            filter: blur(20px);
            border-radius: 999px;
            z-index: -1;
        }

        .browser-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .browser-dots {
            display: flex;
            gap: 8px;
        }

        .browser-dots span {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #d7dee4;
        }

        .browser-address {
            flex: 1;
            height: 36px;
            border-radius: 999px;
            background: #eef4f8;
        }

        .dashboard-card {
            border-radius: 24px;
            background: var(--white);
            border: 1px solid rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .dashboard-top {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 18px;
            background: #f7fafc;
        }

        .metric {
            padding: 14px;
            border-radius: 18px;
            background: var(--white);
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .metric span {
            display: block;
            font-size: 12px;
            color: var(--text);
            font-weight: 700;
        }

        .metric strong {
            display: block;
            margin-top: 8px;
            font-size: 20px;
            font-weight: 800;
        }

        .table-wrap {
            padding: 18px;
        }

        .table-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .table-title strong {
            font-size: 16px;
            font-weight: 800;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(0, 180, 216, 0.12);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px 10px;
            font-size: 14px;
        }

        th {
            color: var(--text);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        tr + tr td {
            border-top: 1px solid rgba(15, 23, 42, 0.06);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
        }

        .status-ready { background: rgba(34, 197, 94, 0.12); color: #15803d; }
        .status-process { background: rgba(245, 158, 11, 0.14); color: #b45309; }
        .status-new { background: rgba(0, 180, 216, 0.12); color: var(--primary); }

        .proof-bar {
            padding: 24px 28px;
            border-radius: 30px;
            background: var(--white);
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: var(--shadow-soft);
        }

        .proof-bar p {
            margin: 0 0 18px;
            text-align: center;
            font-size: 18px;
            font-weight: 800;
        }

        .logo-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .logo-box {
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(180deg, #eef3f7 0%, #e3eaf0 100%);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .card-grid {
            display: grid;
            gap: 18px;
            margin-top: 34px;
        }

        .feature-card,
        .testimonial-card,
        .pricing-card,
        .step-card,
        .footer-card {
            background: var(--white);
            border: 1px solid rgba(15, 23, 42, 0.07);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
        }

        .feature-card {
            padding: 26px;
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: rgba(0, 180, 216, 0.12);
            color: var(--primary);
            margin-bottom: 18px;
        }

        .feature-card h3,
        .step-card h3,
        .pricing-card h3,
        .testimonial-card h3 {
            margin: 0 0 10px;
            font-size: 21px;
            font-weight: 800;
        }

        .feature-card p,
        .step-card p,
        .pricing-card p,
        .testimonial-card p {
            margin: 0;
            color: var(--text);
            line-height: 1.7;
            font-size: 15px;
        }

        .steps-grid,
        .pricing-grid,
        .testimonial-grid,
        .footer-grid {
            display: grid;
            gap: 18px;
            margin-top: 34px;
        }

        .step-card {
            padding: 28px;
            position: relative;
            overflow: hidden;
        }

        .step-number {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            display: grid;
            place-items: center;
            margin-bottom: 22px;
            background: linear-gradient(135deg, var(--primary), #7ae6f9);
            color: var(--white);
            font-size: 30px;
            font-weight: 800;
            box-shadow: 0 20px 30px rgba(0, 180, 216, 0.24);
        }

        .pricing-card {
            padding: 28px;
            position: relative;
        }

        .promo-strip {
            margin-top: 28px;
            display: grid;
            gap: 16px;
            padding: 20px 22px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.14), rgba(15, 23, 42, 0.06));
            border: 1px solid rgba(0, 180, 216, 0.18);
            box-shadow: var(--shadow-soft);
        }

        .promo-strip strong {
            display: block;
            font-size: 22px;
            font-weight: 800;
            color: var(--secondary);
        }

        .promo-strip p {
            margin: 6px 0 0;
            color: var(--text);
            font-size: 15px;
            line-height: 1.7;
        }

        .voucher-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            padding: 12px 18px;
            border-radius: 999px;
            background: var(--secondary);
            color: var(--white);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .pricing-card.popular {
            border: 2px solid var(--primary);
            transform: translateY(-6px);
        }

        .popular-badge {
            position: absolute;
            right: 24px;
            top: 20px;
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(0, 180, 216, 0.14);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }

        .price {
            margin: 8px 0 18px;
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
        }

        .price small {
            font-size: 16px;
            color: var(--text);
            font-weight: 700;
        }

        .pricing-list {
            display: grid;
            gap: 12px;
            margin: 22px 0 28px;
            padding: 0;
        }

        .pricing-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
        }

        .check {
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: rgba(0, 180, 216, 0.14);
            color: var(--primary);
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .testimonial-card {
            padding: 26px;
        }

        .testimonial-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), #84ecff);
            color: var(--white);
            font-weight: 800;
        }

        .stars {
            color: #f59e0b;
            letter-spacing: 0.12em;
            font-size: 14px;
            margin-top: 4px;
        }

        .cta-banner {
            background: var(--secondary);
            color: var(--white);
            border-radius: 36px;
            padding: 40px 28px;
            display: grid;
            gap: 20px;
            align-items: center;
        }

        .cta-banner h2 {
            margin: 0 0 10px;
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.06;
        }

        .cta-banner p {
            margin: 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: 18px;
            line-height: 1.7;
        }

        footer {
            padding: 24px 0 48px;
        }

        .footer-wrap {
            background: var(--white);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 32px;
            padding: 36px 24px;
            box-shadow: var(--shadow-soft);
        }

        .footer-brand p {
            margin: 14px 0 0;
            color: var(--text);
            line-height: 1.7;
            max-width: 280px;
        }

        .footer-column h4 {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 800;
        }

        .footer-column a {
            display: block;
            color: var(--text);
            margin-bottom: 10px;
            font-size: 15px;
        }

        .footer-bottom {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            color: var(--text);
            font-size: 14px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (min-width: 768px) {
            .container { width: min(var(--container), calc(100% - 56px)); }
            .nav-links { display: inline-flex; }
            .hero-grid { grid-template-columns: 1.05fr 0.95fr; padding: 84px 0 64px; }
            .logo-row { grid-template-columns: repeat(5, 1fr); }
            .card-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid,
            .pricing-grid,
            .testimonial-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1.2fr repeat(4, 1fr); }
            .cta-banner { grid-template-columns: 1.2fr auto; padding: 48px 54px; }
            .promo-strip { grid-template-columns: 1fr auto; align-items: center; }
        }

        @media (min-width: 1024px) {
            .card-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 767px) {
            .navbar { min-height: 76px; }
            .brand { font-size: 21px; }
            .section { padding: 58px 0; }
            .dashboard-top { grid-template-columns: 1fr; }
            .proof-bar p { text-align: left; }
            .price { font-size: 36px; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container navbar">
            <a href="#top" class="brand">
                <img src="{{ asset('logo.png') }}" alt="WashKita Logo" class="brand-mark">
                WashKita
            </a>
            <nav class="nav-links">
                <a href="#fitur">Fitur</a>
                <a href="#harga">Harga</a>
                <a href="#testimoni">Testimoni</a>
                <a href="#kontak">Kontak</a>
            </nav>
            <a href="{{ route('register.owner') }}" class="btn btn-primary">Coba Gratis</a>
        </div>
    </header>

    <main id="top">
        <section class="hero">
            <div class="container hero-grid">
                <div class="reveal">
                    <!-- <span class="eyebrow">SaaS Laundry Modern</span> -->
                    <h1>Aplikasi Manajemen Laundry Modern untuk Bisnis Laundry</h1>
                    <p>WashKita membantu pemilik usaha laundry mencatat order, memantau status, dan mengelola keuangan — dari satu dashboard.</p>
                    <div class="hero-actions">
                        <a href="{{ route('register.owner') }}" class="btn btn-primary">Mulai Gratis 14 Hari</a>
                        <a href="#fitur" class="btn btn-secondary">Lihat Demo</a>
                    </div>
                    <div class="hero-note">Software laundry berbasis web tanpa kartu kredit. Cocok untuk UMKM laundry kiloan, satuan, maupun koin multi-cabang.</div>
                </div>

                <div class="mockup-shell reveal">
                    <div class="browser-bar">
                        <div class="browser-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="browser-address"></div>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-top">
                            <div class="metric">
                                <span>Order Hari Ini</span>
                                <strong>128</strong>
                            </div>
                            <div class="metric">
                                <span>Siap Diambil</span>
                                <strong>37</strong>
                            </div>
                            <div class="metric">
                                <span>Pendapatan</span>
                                <strong>Rp 4,8 jt</strong>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <div class="table-title">
                                <strong>Daftar Order Terbaru</strong>
                                <span class="badge">Live Dashboard</span>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Rina Putri</td>
                                        <td>Cuci Lipat 5 Kg</td>
                                        <td><span class="status-pill status-process">Diproses</span></td>
                                    </tr>
                                    <tr>
                                        <td>Dimas Yoga</td>
                                        <td>Dry Clean</td>
                                        <td><span class="status-pill status-ready">Siap Diambil</span></td>
                                    </tr>
                                    <tr>
                                        <td>Ayu Lestari</td>
                                        <td>Cuci Express</td>
                                        <td><span class="status-pill status-new">Baru Masuk</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="section">
            <div class="container">
                <div class="proof-bar reveal">
                    <p>Dipercaya oleh 500+ usaha laundry di Indonesia</p>
                    <div class="logo-row">
                        <div class="logo-box"></div>
                        <div class="logo-box"></div>
                        <div class="logo-box"></div>
                        <div class="logo-box"></div>
                        <div class="logo-box"></div>
                    </div>
                </div>
            </div>
        </section> -->

        <section id="fitur" class="section">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Fitur Utama</span>
                    <h2 class="section-title">Semua yang kamu butuhkan, dalam satu aplikasi manajemen laundry</h2>
                    <p class="section-copy">Dari meja aplikasi kasir laundry sampai laporan keuangan, semua alur kerja tersusun rapi agar operasional lebih cepat dan keputusan bisnis lebih percaya diri dengan sistem laundry digital kami.</p>
                </div>

                <div class="card-grid">
                    <article class="feature-card reveal">
                        <div class="feature-icon">🧺</div>
                        <h3>Manajemen Order</h3>
                        <p>Catat dan pantau status cucian dari masuk hingga selesai tanpa spreadsheet yang berantakan.</p>
                    </article>
                    <article class="feature-card reveal">
                        <div class="feature-icon">💬</div>
                        <h3>Notifikasi Pelanggan</h3>
                        <p>Kirim update otomatis via WhatsApp saat order siap supaya pelanggan datang tepat waktu.</p>
                    </article>
                    <article class="feature-card reveal">
                        <div class="feature-icon">📈</div>
                        <h3>Laporan Keuangan</h3>
                        <p>Lihat pemasukan harian, mingguan, dan bulanan secara real-time dari satu tempat.</p>
                    </article>
                    <article class="feature-card reveal">
                        <div class="feature-icon">🏪</div>
                        <h3>Multi-Cabang</h3>
                        <p>Kelola beberapa outlet dari satu akun dengan visibilitas penuh untuk setiap cabang.</p>
                    </article>
                    <article class="feature-card reveal">
                        <div class="feature-icon">👥</div>
                        <h3>Manajemen Karyawan</h3>
                        <p>Atur jadwal dan performa staf laundry kamu supaya operasional tetap efisien setiap hari.</p>
                    </article>
                    <article class="feature-card reveal">
                        <div class="feature-icon">📱</div>
                        <h3>Aplikasi Mobile</h3>
                        <p>Akses dashboard dan pantau bisnis dari mana saja lewat perangkat iOS dan Android.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="preview" class="section" style="background: var(--white); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Preview Aplikasi</span>
                    <h2 class="section-title">Tampilan Modern & Mudah Digunakan</h2>
                    <p class="section-copy">Desain antarmuka yang bersih membantu tim laundry kamu fokus pada pekerjaan tanpa terganggu navigasi yang rumit.</p>
                </div>

                <div class="card-grid" style="margin-top: 48px; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));">
                    <div class="reveal">
                        <div class="mockup-shell" style="padding: 12px; background: #f8fafc;">
                            <p style="font-size: 11px; font-weight: 800; color: var(--text); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding-left: 8px;">Dashboard Utama</p>
                            <img src="{{ asset('feature/dashboard.png') }}" alt="Dashboard" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.08); width: 100%;">
                        </div>
                    </div>
                    <div class="reveal">
                        <div class="mockup-shell" style="padding: 12px; background: #f8fafc;">
                            <p style="font-size: 11px; font-weight: 800; color: var(--text); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding-left: 8px;">Halaman Kasir</p>
                            <img src="{{ asset('feature/kasir.png') }}" alt="Kasir" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.08); width: 100%;">
                        </div>
                    </div>
                    <div class="reveal">
                        <div class="mockup-shell" style="padding: 12px; background: #f8fafc;">
                            <p style="font-size: 11px; font-weight: 800; color: var(--text); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding-left: 8px;">Daftar Transaksi</p>
                            <img src="{{ asset('feature/transaction.png') }}" alt="Transactions" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.08); width: 100%;">
                        </div>
                    </div>
                    <div class="reveal">
                        <div class="mockup-shell" style="padding: 12px; background: #f8fafc;">
                            <p style="font-size: 11px; font-weight: 800; color: var(--text); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding-left: 8px;">Laporan Mendalam</p>
                            <img src="{{ asset('feature/Report.png') }}" alt="Reports" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.08); width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
        </section>
                <section id="harga" class="section">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Harga</span>
                    <h2 class="section-title">Pilih paket sesuai tahap bisnis laundry kamu</h2>
                    <p class="section-copy">Paket WashKita dibuat simpel dan langsung nyambung dengan kebutuhan operasional laundry, dari outlet pertama sampai bisnis dengan banyak cabang.</p>
                </div>

                <div class="promo-strip reveal">
                    <div>
                        <strong>Promo spesial bulan ini, diskon 50% untuk semua paket berbayar</strong>
                        <p>Pakai kode voucher <b>WASHKITAPROMO</b> saat daftar untuk dapat harga promo dan mulai digitalisasi laundry dengan biaya lebih hemat.</p>
                    </div>
                    <div class="voucher-code">WASHKITAPROMO</div>
                </div>

                <div class="pricing-grid">
                    <article class="pricing-card reveal">
                        <h3>Basic</h3>
                        <div class="price">Rp 30.000 <small>/bln</small></div>
                        <p>Paket ringan untuk laundry yang ingin operasional harian lebih rapi dan cepat.</p>
                        <ul class="pricing-list">
                            <li><span class="check">✓</span>1 outlet aktif</li>
                            <li><span class="check">✓</span>Dashboard</li>
                            <li><span class="check">✓</span>Aplikasi kasir laundry (POS)</li>
                            <li><span class="check">✓</span>Pelanggan dan staff</li>
                            <li><span class="check">✓</span>Pengaturan, role akses, dan outlet</li>
                        </ul>
                        <a href="{{ route('register.owner') }}" class="btn btn-secondary" style="width:100%;">Pilih Basic</a>
                    </article>

                    <article class="pricing-card popular reveal">
                        <span class="popular-badge">Paling Populer</span>
                        <h3>Pro Plan</h3>
                        <div class="price">Rp 55.000 <small>/bln</small></div>
                        <p>Pilihan ideal untuk laundry berkembang yang butuh fitur operasional lebih lengkap.</p>
                        <ul class="pricing-list">
                            <li><span class="check">✓</span>3 outlet aktif</li>
                            <li><span class="check">✓</span>Aplikasi POS laundry lengkap (kasir, transaksi)</li>
                            <li><span class="check">✓</span>Mesin, layanan, dan add on</li>
                            <li><span class="check">✓</span>Laporan dan staff</li>
                            <li><span class="check">✓</span>Multi outlet</li>
                        </ul>
                        <a href="{{ route('register.owner') }}" class="btn btn-primary" style="width:100%;">Pilih Pro Plan</a>
                    </article>

                    <article class="pricing-card reveal">
                        <h3>Enterprise</h3>
                        <div class="price">Rp 80.000 <small>/bln</small></div>
                        <p>Untuk bisnis laundry yang ingin fitur penuh, kontrol cabang, dan dukungan promo penjualan.</p>
                        <ul class="pricing-list">
                            <li><span class="check">✓</span>Outlet dan staff tanpa batas kaku</li>
                            <li><span class="check">✓</span>Dashboard, kasir, transaksi, pelanggan</li>
                            <li><span class="check">✓</span>Mesin, layanan, add on, dan laporan</li>
                            <li><span class="check">✓</span>Role akses dan multi outlet</li>
                            <li><span class="check">✓</span>Fitur promo untuk dorong penjualan</li>
                        </ul>
                        <a href="{{ route('register.owner') }}" class="btn btn-dark" style="width:100%;">Pilih Enterprise</a>
                    </article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Cara Kerja</span>
                    <h2 class="section-title">Mulai dalam tiga langkah sederhana</h2>
                    <p class="section-copy">WashKita dirancang untuk langsung dipakai tanpa setup yang rumit. Tim laundry kamu bisa adaptasi lebih cepat sejak hari pertama.</p>
                </div>

                <div class="steps-grid">
                    <article class="step-card reveal">
                        <div class="step-number">1</div>
                        <h3>Daftar akun gratis</h3>
                        <p>Buat akun owner, pilih paket awal, lalu siapkan outlet pertama kamu dalam hitungan menit.</p>
                    </article>
                    <article class="step-card reveal">
                        <div class="step-number">2</div>
                        <h3>Input order pelanggan</h3>
                        <p>Masukkan pesanan, jenis layanan, biaya, dan estimasi selesai dari dashboard yang mudah dipahami.</p>
                    </article>
                    <article class="step-card reveal">
                        <div class="step-number">3</div>
                        <h3>Pantau & kelola bisnis</h3>
                        <p>Lihat performa outlet, status cucian, dan arus pemasukan tanpa harus cek banyak tempat.</p>
                    </article>
                </div>
            </div>
        </section>



        <section id="testimoni" class="section">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Testimoni</span>
                    <h2 class="section-title">Pemilik laundry suka karena operasional jadi lebih tenang</h2>
                    <p class="section-copy">WashKita adalah aplikasi laundry online yang membantu bisnis laundry di berbagai kota mengurangi kerja manual, mempercepat pelayanan, dan menjaga pelanggan tetap puas dengan aplikasi laundry terbaik di kelasnya.</p>
                </div>

                <div class="testimonial-grid">
                    <article class="testimonial-card reveal">
                        <div class="testimonial-head">
                            <div class="avatar">AN</div>
                            <div>
                                <h3 style="font-size:18px;margin:0;">Anisa Putri</h3>
                                <p style="margin:4px 0 0;">Bandung</p>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                        <p>"Sejak pakai WashKita, order yang tadinya sering tercecer sekarang semua rapi dan customer jadi lebih cepat dilayani."</p>
                    </article>
                    <article class="testimonial-card reveal">
                        <div class="testimonial-head">
                            <div class="avatar">RP</div>
                            <div>
                                <h3 style="font-size:18px;margin:0;">Rian Prakoso</h3>
                                <p style="margin:4px 0 0;">Surabaya</p>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                        <p>"Yang paling terasa itu laporan hariannya. Saya bisa cek pemasukan outlet tanpa harus tanya admin satu per satu."</p>
                    </article>
                    <article class="testimonial-card reveal">
                        <div class="testimonial-head">
                            <div class="avatar">DS</div>
                            <div>
                                <h3 style="font-size:18px;margin:0;">Dewi Safitri</h3>
                                <p style="margin:4px 0 0;">Yogyakarta</p>
                                <div class="stars">★★★★★</div>
                            </div>
                        </div>
                        <p>"Tampilan aplikasinya enak dipakai staff saya. Training jadi singkat dan transisi dari manual ke digital jauh lebih mulus."</p>
                    </article>
                </div>
            </div>
        </section>

        </section>

        <section id="kontak" class="section">
            <div class="container">
                <div class="reveal">
                    <span class="eyebrow">Hubungi Kami</span>
                    <h2 class="section-title">Ada Pertanyaan? Kami Siap Membantu</h2>
                    <p class="section-copy">Punya pertanyaan seputar fitur, paket, atau butuh bantuan teknis? Jangan ragu untuk menghubungi tim support kami melalui saluran di bawah ini.</p>
                </div>

                <div class="card-grid reveal" style="margin-top: 48px;">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: rgba(34, 197, 94, 0.12); color: #15803d;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <h3>WhatsApp / Phone</h3>
                        <p>Konsultasi langsung melalui WhatsApp atau telepon untuk respon yang lebih cepat.</p>
                        <a href="https://wa.me/6285262827436" target="_blank" class="btn btn-secondary" style="margin-top: 20px; width: 100%;">0852-6282-7436</a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: rgba(0, 180, 216, 0.12); color: var(--primary);">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <h3>Email Support</h3>
                        <p>Kirimkan pertanyaan atau laporan kendala Anda melalui email resmi kami.</p>
                        <a href="mailto:contact.washkita@gmail.com" class="btn btn-secondary" style="margin-top: 20px; width: 100%;">contact.washkita@gmail.com</a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: rgba(236, 72, 153, 0.12); color: #db2777;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </div>
                        <h3>Instagram</h3>
                        <p>Ikuti kami untuk update fitur terbaru, tips bisnis laundry, dan promo menarik lainnya.</p>
                        <a href="https://www.instagram.com/wash.kita" target="_blank" class="btn btn-secondary" style="margin-top: 20px; width: 100%;">@wash.kita</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="blog">
        <div class="container">
            <div class="footer-wrap">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="#top" class="brand">
                            <img src="{{ asset('logo.png') }}" alt="WashKita - Aplikasi Manajemen Laundry" class="brand-mark">
                            WashKita
                        </a>
                        <p>Aplikasi manajemen usaha laundry untuk owner yang ingin operasional rapi, cepat, dan siap berkembang dengan aplikasi POS laundry modern.</p>
                    </div>

                    <div class="footer-column">
                        <h4>Produk</h4>
                        <a href="#fitur">Fitur</a>
                        <a href="#harga">Harga</a>
                        <a href="#testimoni">Testimoni</a>
                    </div>

                    <div class="footer-column">
                        <h4>Perusahaan</h4>
                        <a href="{{ route('register.owner') }}">Tentang</a>
                        <a href="{{ route('register.owner') }}">Karier</a>
                        <a href="{{ route('register.owner') }}">Mitra</a>
                    </div>

                    <div class="footer-column">
                        <h4>Support</h4>
                        <a href="{{ route('login') }}">Pusat Bantuan</a>
                        <a href="#kontak">Hubungi Kami</a>
                        <a href="{{ route('login') }}">Status Sistem</a>
                    </div>

                    <div class="footer-column">
                        <h4>Social Media</h4>
                        <a href="https://www.instagram.com/wash.kita" target="_blank">Instagram</a>
                        <a href="#">LinkedIn</a>
                        <a href="#">YouTube</a>
                    </div>
                </div>

                <div class="footer-bottom">© 2025 WashKita. All rights reserved.</div>
            </div>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, { threshold: 0.14 });

        document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));
    </script>
</body>
</html>
