<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .item-row { display: flex; justify-content: space-between; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 10px; }
        .total-section { font-weight: bold; }
        @media print {
            body { width: 100%; margin: 0; padding: 0; }
            @page { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1>LAUNDRY TRACK</h1>
        <p>Jl. Laundry No. 123, Jakarta<br>Telp: 0812-3456-7890</p>
    </div>

    <div class="divider"></div>

    <div>
        <p>No: {{ $transaction->transaction_number }}<br>
           Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
           Kasir: {{ $transaction->cashier->nama ?? '-' }}<br>
           Plgn: {{ $transaction->member ? $transaction->member->nama : 'Non Member' }}</p>
    </div>

    <div class="divider"></div>

    <div class="items">
        @if($transaction->transaction_type === 'SELF_SERVICE')
            @foreach($transaction->selfServiceDetails as $detail)
            <div class="item-row">
                <span>{{ $detail->machine->machine_code }} - {{ $detail->duration_minutes }}m</span>
                <span>Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
            </div>
            @endforeach
        @else
            {{-- Drop Off --}}
            @foreach($transaction->servicePackages as $pkg)
            <div class="item-row">
                <span>{{ $pkg->nama_paket }} ({{ $pkg->pivot->weight }} {{ $pkg->satuanSingkat() }})</span>
                <span>Rp {{ number_format($pkg->pivot->price * $pkg->pivot->weight, 0, ',', '.') }}</span>
            </div>
            @if($pkg->pivot->note)
            <div style="font-size: 9px; color: #666; margin-bottom: 5px; margin-left: 5px;">
                Note: {{ $pkg->pivot->note }}
            </div>
            @endif
            @endforeach

            @foreach($transaction->addonOptions as $addon)
            <div class="item-row">
                <span>{{ $addon->nama }}</span>
                <span>Rp {{ number_format($addon->pivot->price, 0, ',', '.') }}</span>
            </div>
            @endforeach

            @if($transaction->items->count() > 0)
            <div class="divider"></div>
            <div style="font-size: 10px; font-weight: bold; margin-bottom: 5px;">DETAIL ITEM:</div>
            @foreach($transaction->items as $item)
            <div class="item-row" style="font-size: 10px;">
                <span>{{ $item->qty }}x {{ $item->nama_item }}</span>
                <span>{{ $item->note }}</span>
            </div>
            @endforeach
            @endif
        @endif
    </div>

    <div class="divider"></div>

    <div class="total-section">
        <div class="item-row">
            <span>SUBTOTAL</span>
            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($transaction->discount_amount > 0)
        <div class="item-row">
            <span>DISKON ({{ number_format($transaction->discount_percent, 0) }}%)</span>
            <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($transaction->tax_amount > 0)
        <div class="item-row">
            <span>PAJAK ({{ number_format($transaction->tax_percent, 0) }}%)</span>
            <span>+ Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="item-row" style="font-size: 14px; margin-top: 5px;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="payment-section">
        <div class="item-row">
            <span>BAYAR ({{ $transaction->payment_method }})</span>
            <span>Rp {{ number_format($transaction->amount_received, 0, ',', '.') }}</span>
        </div>
        <div class="item-row">
            <span>KEMBALI</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 20px;">
        <p>Terima kasih atas kunjungan Anda!<br>Pakaian bersih, hati senang.</p>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">CETAK STRUK</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; margin-left: 10px;">TUTUP</button>
    </div>

    <script>
        // Auto print when loaded
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
