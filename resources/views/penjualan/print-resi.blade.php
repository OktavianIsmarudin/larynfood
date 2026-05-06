<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 10px;
            color: #666;
            margin: 0;
        }

        .info-section {
            margin-bottom: 5mm;
            padding-bottom: 5mm;
            border-bottom: 1px dashed #ccc;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2mm;
            font-size: 11px;
        }

        .info-label {
            font-weight: bold;
            width: 35%;
        }

        .info-value {
            width: 65%;
            text-align: right;
            word-wrap: break-word;
        }

        .product-section {
            background: #f9f9f9;
            padding: 3mm;
            margin-bottom: 5mm;
            border: 1px solid #ddd;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 2mm;
            font-size: 12px;
        }

        .product-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
            font-size: 10px;
        }

        .price-section {
            margin-bottom: 5mm;
            padding-bottom: 5mm;
            border-bottom: 1px dashed #ccc;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2mm;
            font-size: 11px;
        }

        .price-label {
            font-weight: normal;
        }

        .price-value {
            text-align: right;
            font-weight: normal;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0;
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 2mm;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #666;
        }

        .status {
            text-align: center;
            margin-top: 3mm;
            padding: 2mm;
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: #2e7d32;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }
        }
    </style>
</head>
<body>
    @php
        $cleanKeterangan = $penjualan->keterangan;
        if (!empty($cleanKeterangan)) {
            $cleanKeterangan = preg_replace('/\[AUTO-ORDER:[^\]]+\]\[ITEM:[^\]]+\]\s*/', '', $cleanKeterangan);
            $cleanKeterangan = preg_replace('/^Auto\s+sinkron\s+dari\s+tracking\s+order\s+[^\.]+\.\s*/i', '', $cleanKeterangan);
            $cleanKeterangan = trim((string) $cleanKeterangan);
        }
    @endphp

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>🛒 RESI PENJUALAN</h1>
            <p>Invoice No. #{{ str_pad($penjualan->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- Info Dasar --}}
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d-m-Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $penjualan->nama_customer_snapshot ?? $penjualan->customer->nama_customer ?? '-' }}</span>
            </div>
            @if ($penjualan->customer && $penjualan->customer->telepon)
            <div class="info-row">
                <span class="info-label">Kontak:</span>
                <span class="info-value">{{ $penjualan->customer->telepon }}</span>
            </div>
            @endif
        </div>

        {{-- Produk --}}
        <div class="product-section">
            <div class="product-name">
                {{ $penjualan->nama_produk_display }}
            </div>
            <div class="product-detail">
                <span>Jumlah: {{ $penjualan->jumlah_pcs }} PCS</span>
                <span>@ Rp {{ number_format($penjualan->harga_satuan, 0, ',', '.') }}</span>
            </div>
            <div class="product-detail">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Rincian Pembayaran --}}
        <div class="price-section">
            {{-- Subtotal --}}
            <div class="price-row">
                <span class="price-label">Subtotal:</span>
                <span class="price-value">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>

            {{-- Diskon --}}
            @if ($penjualan->nilai_diskon > 0)
            <div class="price-row">
                <span class="price-label">
                    Diskon ({{ $penjualan->label_diskon }})
                </span>
                <span class="price-value" style="color: #16a34a;">- Rp {{ number_format($penjualan->nilai_diskon, 0, ',', '.') }}</span>
            </div>
            @endif

            {{-- Ongkir --}}
            @if ($penjualan->ongkir > 0)
            <div class="price-row">
                <span class="price-label">Ongkir:</span>
                <span class="price-value" style="color: #d97706;">+ Rp {{ number_format($penjualan->ongkir, 0, ',', '.') }}</span>
            </div>
            @endif

            {{-- Total Bayar --}}
            <div class="total-row">
                <span class="total-label">TOTAL BAYAR:</span>
                <span class="total-value">Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div style="margin-bottom: 3mm; padding: 2mm 0; border-bottom: 1px dashed #ccc;">
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span><strong>Pembayaran:</strong></span>
                <span>{{ $penjualan->metodePembayaran->nama_metode ?? 'Tunai' }}</span>
            </div>
        </div>

        {{-- Status Pembayaran --}}
        <div style="margin-bottom: 5mm;">
            @if ($penjualan->status_pembayaran == 'lunas')
            <div class="status" style="background: #e8f5e9; border-color: #4caf50; color: #2e7d32;">
                ✓ LUNAS
            </div>
            @elseif ($penjualan->status_pembayaran == 'dp')
            <div class="status" style="background: #fff3e0; border-color: #ff9800; color: #e65100;">
                ⏳ DP (Down Payment)
            </div>
            @else
            <div class="status" style="background: #ffebee; border-color: #f44336; color: #c62828;">
                ⚠ UTANG
            </div>
            @endif
        </div>

        {{-- Keterangan --}}
        @if (!empty($cleanKeterangan))
        <div style="margin-bottom: 3mm; padding: 2mm; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 2mm; font-size: 10px;">
            <strong>Catatan:</strong><br>
            {{ $cleanKeterangan }}
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p style="margin-bottom: 2mm;">Terima kasih atas pembelian Anda!</p>
            <p>{{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
