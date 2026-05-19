<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Laundry - {{ $outletName }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #00B4D8;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #00B4D8;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
        .report-title {
            text-align: center;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table th, .info-table td {
            padding: 12px 15px;
            text-align: left;
        }
        .info-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            width: 35%;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-table td {
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }
        .payment-section {
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .payment-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px dashed #99f6e4;
            padding-bottom: 8px;
        }
        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .payment-label {
            display: table-cell;
            color: #334155;
            font-weight: 500;
        }
        .payment-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="brand">WashKita</h1>
                    <p class="brand-subtitle">Aplikasi Manajemen Laundry Modern</p>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <p style="font-size: 11px; color: #64748b; margin: 0;">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">Laporan Kinerja Laundry</div>

    <table class="info-table">
        <tr>
            <th>Periode</th>
            <td>{{ $periodString }}</td>
        </tr>
        <tr>
            <th>Outlet</th>
            <td>{{ $outletName }}</td>
        </tr>
        <tr>
            <th>Total Omzet</th>
            <td style="font-weight: bold; color: #00B4D8; font-size: 16px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Order Selesai</th>
            <td>{{ number_format($completedOrdersCount, 0, ',', '.') }} Transaksi</td>
        </tr>
        <tr>
            <th>Layanan Terlaris</th>
            <td>{{ $popularService }}</td>
        </tr>
        <tr>
            <th>Mesin Paling Banyak Terpakai</th>
            <td>{{ $mostUsedMachine }}</td>
        </tr>
    </table>

    <div class="payment-section">
        <h3 class="payment-title">Rincian Metode Pembayaran</h3>
        <div class="payment-row">
            <span class="payment-label">Total Pembayaran QRIS</span>
            <span class="payment-value">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</span>
        </div>
        <div class="payment-row">
            <span class="payment-label">Total Pembayaran Cash</span>
            <span class="payment-value">Rp {{ number_format($cashTotal, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem WashKita. Hak Cipta &copy; {{ date('Y') }} WashKita.
    </div>
</body>
</html>
