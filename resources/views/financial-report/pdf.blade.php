<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 25px;
            color: #2c3e50;
            font-size: 12px;
            line-height: 1.4;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0D47A1;
        }
        .header h1 {
            font-size: 22px;
            color: #0D47A1;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .header .company {
            font-size: 16px;
            color: #1565C0;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .header .periode {
            font-size: 13px;
            color: #455a64;
            background: #E3F2FD;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
        }
        
        /* Summary Cards */
        .summary-section {
            margin-bottom: 25px;
        }
        .summary-title {
            font-size: 14px;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 12px;
            padding-left: 8px;
            border-left: 4px solid #2E7D32;
        }
        .summary-cards {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            vertical-align: top;
        }
        .summary-card.pendapatan {
            background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
            border: 2px solid #4CAF50;
        }
        .summary-card.hpp {
            background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
            border: 2px solid #FF9800;
        }
        .summary-card.laba {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border: 2px solid #2196F3;
        }
        .summary-card.margin {
            background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%);
            border: 2px solid #9C27B0;
        }
        .summary-card .label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .summary-card.pendapatan .label { color: #2E7D32; }
        .summary-card.hpp .label { color: #E65100; }
        .summary-card.laba .label { color: #1565C0; }
        .summary-card.margin .label { color: #7B1FA2; }
        
        .summary-card .value {
            font-size: 16px;
            font-weight: 700;
        }
        .summary-card.pendapatan .value { color: #1B5E20; }
        .summary-card.hpp .value { color: #E65100; }
        .summary-card.laba .value { color: #0D47A1; }
        .summary-card.margin .value { color: #6A1B9A; }
        
        .summary-card .info {
            font-size: 9px;
            margin-top: 5px;
            color: #616161;
        }
        
        /* Detail Info Box */
        .detail-box {
            background: #FAFAFA;
            border: 1px solid #E0E0E0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .detail-box-title {
            font-size: 11px;
            font-weight: 600;
            color: #455A64;
            margin-bottom: 8px;
        }
        .detail-box-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        .detail-box-label {
            display: table-cell;
            width: 50%;
            font-size: 10px;
            color: #757575;
        }
        .detail-box-value {
            display: table-cell;
            width: 50%;
            font-size: 10px;
            color: #424242;
            text-align: right;
            font-weight: 500;
        }
        
        /* Table Section */
        .table-section {
            margin-bottom: 25px;
        }
        .table-title {
            font-size: 14px;
            font-weight: 700;
            color: #1565C0;
            margin-bottom: 12px;
            padding-left: 8px;
            border-left: 4px solid #1565C0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table thead tr {
            background: #1565C0;
        }
        table th {
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
        }
        table th.text-right {
            text-align: right;
        }
        table th.text-center {
            text-align: center;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #E0E0E0;
        }
        table tbody tr:nth-child(even) {
            background: #FAFAFA;
        }
        table tbody tr:hover {
            background: #E3F2FD;
        }
        table .text-right {
            text-align: right;
        }
        table .text-center {
            text-align: center;
        }
        table .text-bold {
            font-weight: 600;
        }
        table .text-success {
            color: #2E7D32;
        }
        table .text-danger {
            color: #C62828;
        }
        table tfoot tr {
            background: #E3F2FD;
            font-weight: 700;
        }
        table tfoot td {
            border-top: 2px solid #1565C0;
            padding: 10px 8px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E0E0E0;
            text-align: center;
        }
        .footer-line {
            font-size: 10px;
            color: #9E9E9E;
            margin-bottom: 3px;
        }
        .footer-company {
            font-size: 11px;
            color: #616161;
            font-weight: 600;
        }
        
        /* Utils */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #9E9E9E;
            font-style: italic;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <div class="company">LARYN</div>
        <div class="periode">Periode: {{ $laporan['periode']['label'] }}</div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-section">
        <div class="summary-title">RINGKASAN KEUANGAN</div>
        <div class="summary-cards">
            <div class="summary-card pendapatan">
                <div class="label">Pendapatan</div>
                <div class="value">Rp {{ number_format($laporan['ringkasan']['pendapatan'], 0, ',', '.') }}</div>
                <div class="info">Total Bayar Aktual</div>
            </div>
            <div class="summary-card hpp">
                <div class="label">Total HPP</div>
                <div class="value">Rp {{ number_format($laporan['ringkasan']['total_biaya_hpp'], 0, ',', '.') }}</div>
                <div class="info">Harga Pokok Produksi</div>
            </div>
            <div class="summary-card laba">
                <div class="label">{{ $laporan['ringkasan']['laba_rugi'] >= 0 ? 'Laba' : 'Rugi' }}</div>
                <div class="value">Rp {{ number_format(abs($laporan['ringkasan']['laba_rugi']), 0, ',', '.') }}</div>
                <div class="info">Pendapatan - HPP</div>
            </div>
            <div class="summary-card margin">
                <div class="label">Margin</div>
                <div class="value">{{ number_format($laporan['ringkasan']['margin_keuntungan'], 2) }}%</div>
                <div class="info">Profitabilitas</div>
            </div>
        </div>
    </div>

    {{-- Detail Info --}}
    <div class="detail-box">
        <div class="detail-box-title">RINCIAN KOMPONEN PENJUALAN</div>
        <div class="detail-box-row">
            <div class="detail-box-label">Subtotal Sebelum Diskon</div>
            <div class="detail-box-value">Rp {{ number_format($laporan['ringkasan']['total_subtotal'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="detail-box-row">
            <div class="detail-box-label">Total Diskon Diberikan</div>
            <div class="detail-box-value" style="color: #C62828;">- Rp {{ number_format($laporan['ringkasan']['total_diskon'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="detail-box-row">
            <div class="detail-box-label">Total Ongkir</div>
            <div class="detail-box-value" style="color: #2E7D32;">+ Rp {{ number_format($laporan['ringkasan']['total_ongkir'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="detail-box-row" style="border-top: 1px dashed #BDBDBD; padding-top: 8px; margin-top: 5px;">
            <div class="detail-box-label" style="font-weight: 600; color: #1565C0;">= Pendapatan (Total Bayar)</div>
            <div class="detail-box-value" style="font-weight: 700; color: #1565C0;">Rp {{ number_format($laporan['ringkasan']['pendapatan'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Breakdown Table --}}
    <div class="table-section">
        <div class="table-title">BREAKDOWN PER PRODUK</div>
        
        @if (!empty($laporan['breakdown_produk']) && count($laporan['breakdown_produk']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 22%;">Produk</th>
                        <th class="text-center" style="width: 8%;">Trx</th>
                        <th class="text-center" style="width: 8%;">Qty</th>
                        <th class="text-right" style="width: 14%;">Pendapatan</th>
                        <th class="text-right" style="width: 14%;">HPP</th>
                        <th class="text-right" style="width: 14%;">Laba</th>
                        <th class="text-center" style="width: 10%;">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($laporan['breakdown_produk'] as $item)
                    @php
                        $laba = $item['laba'] ?? 0;
                        $labaClass = $laba >= 0 ? 'text-success' : 'text-danger';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-bold">{{ $item['nama_produk'] ?? $item['produk'] ?? '-' }}</td>
                        <td class="text-center">{{ $item['transaksi'] ?? 0 }}</td>
                        <td class="text-center">{{ number_format($item['jumlah_pcs'] ?? 0, 0) }}</td>
                        <td class="text-right">Rp {{ number_format($item['total_penjualan'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['total_hpp'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right text-bold {{ $labaClass }}">Rp {{ number_format($laba, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item['margin'] ?? 0, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totalQty = array_sum(array_column($laporan['breakdown_produk'], 'jumlah_pcs'));
                        $totalTransaksi = array_sum(array_column($laporan['breakdown_produk'], 'transaksi'));
                        $totalPendapatan = array_sum(array_column($laporan['breakdown_produk'], 'total_penjualan'));
                        $totalHpp = array_sum(array_column($laporan['breakdown_produk'], 'total_hpp'));
                        $totalLaba = array_sum(array_column($laporan['breakdown_produk'], 'laba'));
                        $marginTotal = $totalPendapatan > 0 ? ($totalLaba / $totalPendapatan) * 100 : 0;
                    @endphp
                    <tr>
                        <td colspan="2" class="text-bold">TOTAL</td>
                        <td class="text-center text-bold">{{ $totalTransaksi }}</td>
                        <td class="text-center text-bold">{{ number_format($totalQty, 0) }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                        <td class="text-right text-bold {{ $totalLaba >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
                        <td class="text-center text-bold">{{ number_format($marginTotal, 2) }}%</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-data">
                Belum ada data penjualan untuk periode ini
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-line">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</div>
        <div class="footer-line">Laporan ini dihasilkan secara otomatis oleh sistem</div>
        <div class="footer-company">&copy; {{ now()->year }} LARYN - Sistem Inventory</div>
    </div>
</body>
</html>
