@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="fas fa-chart-line"></i> Laporan Keuangan</h3>
                <div class="btn-group" role="group">
                    <a href="{{ route('financial-report.filter-harian') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-day"></i> Hari Ini
                    </a>
                    <a href="{{ route('financial-report.filter-bulanan') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-alt"></i> Bulan Ini
                    </a>
                </div>
            </div>

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('financial-report.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Tampilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Keuangan --}}
    <div class="row mb-4">
        {{-- Pendapatan --}}
        <div class="col-md-4 mb-3">
            <div class="card summary-card summary-card-income" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body" style="background: linear-gradient(135deg, #d5f4e6 0%, #a9e4cc 100%); padding: 25px; position: relative;">
                    <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(39, 174, 96, 0.1); border-radius: 50%;"></div>
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1; z-index: 1;">
                            <p style="color: #27ae60; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-arrow-up"></i> Pendapatan
                            </p>
                            <h2 style="color: #27ae60; font-weight: 700; margin-bottom: 5px; font-size: 28px;">
                                Rp {{ number_format($laporan['ringkasan']['pendapatan'], 0, ',', '.') }}
                            </h2>
                            <p style="color: #16a34a; font-size: 12px; margin: 0;">Pendapatan aktual (termasuk ongkir, setelah diskon)</p>
                        </div>
                        <div style="font-size: 60px; color: rgba(39, 174, 96, 0.2); z-index: 0;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Biaya HPP --}}
        <div class="col-md-4 mb-3">
            <div class="card summary-card summary-card-cost" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body" style="background: linear-gradient(135deg, #fdebd0 0%, #fbd3b0 100%); padding: 25px; position: relative;">
                    <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(230, 126, 34, 0.1); border-radius: 50%;"></div>
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1; z-index: 1;">
                            <p style="color: #d68910; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-arrow-down"></i> Total Biaya (HPP)
                            </p>
                            <h2 style="color: #e67e22; font-weight: 700; margin-bottom: 5px; font-size: 28px;">
                                Rp {{ number_format($laporan['ringkasan']['total_biaya_hpp'], 0, ',', '.') }}
                            </h2>
                            <p style="color: #d68910; font-size: 12px; margin: 0;">Harga pokok penjualan</p>
                        </div>
                        <div style="font-size: 60px; color: rgba(230, 126, 34, 0.2); z-index: 0;">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laba / Rugi --}}
        <div class="col-md-4 mb-3">
            <div class="card summary-card summary-card-profit" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba({{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '41, 128, 185, 0.15' : '192, 57, 43, 0.15' }}); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body" style="background: linear-gradient(135deg, {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '#d6eaf8 0%, #aed6f1 100%' : '#fadbd8 0%, #f5b7b1 100%' }}); padding: 25px; position: relative;">
                    <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba({{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '41, 128, 185, 0.1' : '192, 57, 43, 0.1' }}); border-radius: 50%;"></div>
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1; z-index: 1;">
                            <p style="color: {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '#2980b9' : '#c0392b' }}; font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i> Laba / Rugi
                            </p>
                            <h2 style="color: {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '#2980b9' : '#e74c3c' }}; font-weight: 700; margin-bottom: 5px; font-size: 28px;">
                                Rp {{ number_format($laporan['ringkasan']['laba_rugi'], 0, ',', '.') }}
                            </h2>
                            <p style="color: {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '#2980b9' : '#c0392b' }}; font-size: 12px; margin: 0;">
                                Margin: <strong>{{ number_format($laporan['ringkasan']['margin_keuntungan'], 2) }}%</strong>
                            </p>
                        </div>
                        <div style="font-size: 60px; color: rgba({{ $laporan['ringkasan']['laba_rugi'] >= 0 ? '41, 128, 185, 0.2' : '192, 57, 43, 0.2' }}); z-index: 0;">
                            <i class="fas {{ $laporan['ringkasan']['laba_rugi'] >= 0 ? 'fa-smile' : 'fa-frown' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Tambahan --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem;">Total Transaksi</h6>
                    <h4 class="text-primary mb-0">{{ $stats['total_transaksi'] }}</h4>
                    <small class="text-muted" style="font-size: 0.7rem;">penjualan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem;">Rata-rata Transaksi</h6>
                    <h4 class="text-primary mb-0" style="font-size: 1rem;">Rp {{ number_format($stats['rata_rata_transaksi'], 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 0.7rem;">per transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card" style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem; color: #c2185b;"><i class="fas fa-tag"></i> Total Diskon</h6>
                    <h4 class="mb-0" style="font-size: 1rem; color: #c2185b;">Rp {{ number_format($laporan['ringkasan']['total_diskon'] ?? 0, 0, ',', '.') }}</h4>
                    <small style="font-size: 0.7rem; color: #ad1457;">potongan harga</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem; color: #1565c0;"><i class="fas fa-truck"></i> Total Ongkir</h6>
                    <h4 class="mb-0" style="font-size: 1rem; color: #1565c0;">Rp {{ number_format($laporan['ringkasan']['total_ongkir'] ?? 0, 0, ',', '.') }}</h4>
                    <small style="font-size: 0.7rem; color: #0d47a1;">biaya pengiriman</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem;">Margin</h6>
                    <h4 class="text-primary mb-0">{{ number_format($stats['margin_keuntungan'], 2) }}%</h4>
                    <small class="text-muted" style="font-size: 0.7rem;">dari pendapatan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1" style="font-size: 0.75rem;">Periode</h6>
                    <h4 class="text-primary mb-0" style="font-size: 0.85rem;">{{ $laporan['periode']['label'] }}</h4>
                    <small class="text-muted" style="font-size: 0.7rem;">laporan</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown per Produk --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Breakdown Laba per Produk</h5>
                </div>
                <div class="card-body">
                    @if (!empty($laporan['breakdown_produk']) && count($laporan['breakdown_produk']) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Transaksi</th>
                                        <th class="text-end">Jumlah PCS</th>
                                        <th class="text-end">Total Penjualan</th>
                                        <th class="text-end">Total HPP</th>
                                        <th class="text-end">Laba</th>
                                        <th class="text-center">Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($laporan['breakdown_produk'] as $item)
                                    @php
                                        $margin = $item['total_penjualan'] > 0 ? ($item['laba'] / $item['total_penjualan']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $item['nama_produk'] ?? $item['produk'] ?? '-' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $item['transaksi'] ?? 0 }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['jumlah_pcs'] ?? 0, 0) }} PCS
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($item['total_penjualan'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="text-warning">Rp {{ number_format($item['total_hpp'] ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="{{ ($item['laba'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($item['laba'] ?? 0, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $margin >= 20 ? 'bg-success' : ($margin >= 10 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="2">TOTAL</td>
                                        <td class="text-end">
                                            {{ number_format(array_sum(array_column($laporan['breakdown_produk'], 'jumlah_pcs')), 0) }} PCS
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format(array_sum(array_column($laporan['breakdown_produk'], 'total_penjualan')), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format(array_sum(array_column($laporan['breakdown_produk'], 'total_hpp')), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format(array_sum(array_column($laporan['breakdown_produk'], 'laba')), 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Belum ada data penjualan untuk periode ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2">
                <a href="{{ route('financial-report.export-pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                   class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('financial-report.export-excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
<style>
    .summary-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .summary-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .summary-card-income:hover {
        box-shadow: 0 12px 24px rgba(39, 174, 96, 0.25) !important;
    }

    .summary-card-cost:hover {
        box-shadow: 0 12px 24px rgba(230, 126, 34, 0.25) !important;
    }

    .summary-card-profit:hover {
        box-shadow: 0 12px 24px rgba(41, 128, 185, 0.25) !important;
    }

    @media print {
        .btn-group, .btn, [type="date"], [type="submit"], .btn-outline-primary {
            display: none !important;
        }
        .card {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
