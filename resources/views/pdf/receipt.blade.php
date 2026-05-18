<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi - {{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #eee;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #1a1a1a;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 10px;
        }
        .info-section {
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 2px 0;
            font-size: 10px;
            color: #888;
        }
        .info-value {
            display: table-cell;
            text-align: right;
            padding: 2px 0;
            font-size: 10px;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #eee;
            margin: 10px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            text-align: left;
            font-size: 10px;
            color: #888;
            border-bottom: 1px solid #eee;
            padding: 5px 0;
        }
        .items-table td {
            padding: 8px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
            display: block;
        }
        .item-meta {
            font-size: 9px;
            color: #999;
        }
        .text-right {
            text-align: right;
        }
        .summary-section {
            width: 100%;
            display: table;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            padding: 3px 0;
            color: #666;
        }
        .summary-value {
            display: table-cell;
            text-align: right;
            padding: 3px 0;
            font-weight: bold;
        }
        .total-row .summary-label,
        .total-row .summary-value {
            font-size: 14px;
            font-weight: 800;
            color: #1a1a1a;
            border-top: 2px solid #1a1a1a;
            padding-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #eef2ff;
            color: #4338ca;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h2>{{ $transaction->outlet->nama_outlet }}</h2>
            <p>{{ $transaction->outlet->alamat }}</p>
            <p>{{ $transaction->outlet->no_telp }}</p>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">No. Transaksi</div>
                <div class="info-value">{{ $transaction->transaction_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Pelanggan</div>
                <div class="info-value">{{ $transaction->member->nama ?? 'Guest' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kasir</div>
                <div class="info-value">{{ $transaction->cashier->nama }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($transaction->transaction_type === 'SELF_SERVICE')
                    @foreach($transaction->selfServiceDetails as $detail)
                        <tr>
                            <td>
                                <span class="item-name">{{ $detail->machine->machine_code }} - {{ $detail->machine->machine_type }}</span>
                                <span class="item-meta">{{ $detail->duration_minutes }} Menit</span>
                            </td>
                            <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach($transaction->servicePackages as $pkg)
                        <tr>
                            <td>
                                <span class="item-name">{{ $pkg->nama_paket }}</span>
                                <span class="item-meta">{{ max($pkg->pivot->weight, 1) }} {{ $pkg->satuanSingkat() }} x Rp {{ number_format($pkg->pivot->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-right">Rp {{ number_format($pkg->pivot->price * max($pkg->pivot->weight, 1), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @foreach($transaction->addonOptions as $addon)
                        <tr>
                            <td>
                                <span class="item-name">{{ $addon->nama }}</span>
                                <span class="item-meta">Add-on</span>
                            </td>
                            <td class="text-right">Rp {{ number_format($addon->pivot->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="summary-section">
            <div class="summary-row">
                <div class="summary-label">Subtotal</div>
                <div class="summary-value">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</div>
            </div>
            @if($transaction->discount_amount > 0)
                <div class="summary-row">
                    <div class="summary-label">Diskon ({{ $transaction->discount_percent }}%)</div>
                    <div class="summary-value">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</div>
                </div>
            @endif
            @if($transaction->tax_amount > 0)
                <div class="summary-row">
                    <div class="summary-label">Pajak ({{ $transaction->tax_percent }}%)</div>
                    <div class="summary-value">+ Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</div>
                </div>
            @endif
            <div class="summary-row total-row">
                <div class="summary-label">TOTAL</div>
                <div class="summary-value">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Metode Bayar</div>
                <div class="info-value">{{ $transaction->payment_method }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bayar</div>
                <div class="info-value">Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kembalian</div>
                <div class="info-value">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mencuci di {{ $transaction->outlet->nama_outlet }}!</p>
            <p>Simpan kwitansi ini sebagai bukti pengambilan.</p>
            <div style="margin-top: 10px;">
                <span class="status-badge">PAID</span>
            </div>
        </div>
    </div>
</body>
</html>
