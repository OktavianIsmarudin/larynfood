@extends('layouts.app')

@section('title', 'Rekap Pembelian - Laporan')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-chart-pie"></i> Rekap Pembelian</h3>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.purchase-summary') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(13, 71, 161, 0.15); background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); color: white; transition: all 0.3s ease;">
                <div class="card-body" style="position: relative; z-index: 1;">
                    <p style="font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">
                        <i class="fas fa-exchange-alt"></i> Total Transaksi
                    </p>
                    <h2 style="font-weight: 700; margin: 0; font-size: 32px;">{{ $summaryStats['total_transactions'] }}</h2>
                </div>
                <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(46, 125, 50, 0.15); background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%); color: white; transition: all 0.3s ease;">
                <div class="card-body" style="position: relative; z-index: 1;">
                    <p style="font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">
                        <i class="fas fa-boxes"></i> Total Quantity
                    </p>
                    <h2 style="font-weight: 700; margin: 0; font-size: 32px;">{{ number_format($summaryStats['total_qty'], 0) }} <small style="font-size: 16px;">pcs</small></h2>
                </div>
                <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(198, 40, 40, 0.15); background: linear-gradient(135deg, #c62828 0%, #e53935 100%); color: white; transition: all 0.3s ease;">
                <div class="card-body" style="position: relative; z-index: 1;">
                    <p style="font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">
                        <i class="fas fa-shopping-cart"></i> Total Pembelian
                    </p>
                    <h2 style="font-weight: 700; margin: 0; font-size: 28px;">Rp {{ number_format($summaryStats['total_purchases'], 0, ',', '.') }}</h2>
                </div>
                <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.15); background: linear-gradient(135deg, #fbc02d 0%, #f57f17 100%); color: white; transition: all 0.3s ease;">
                <div class="card-body" style="position: relative; z-index: 1;">
                    <p style="font-size: 12px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">
                        <i class="fas fa-chart-bar"></i> Rata-rata/Hari
                    </p>
                    <h2 style="font-weight: 700; margin: 0; font-size: 28px;">Rp {{ number_format($summaryStats['average_purchase'], 0, ',', '.') }}</h2>
                </div>
                <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($purchaseData->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th width="150" class="text-center">Transaksi</th>
                                        <th width="150" class="text-center">Total Qty</th>
                                        <th width="200" class="text-right">Total Pembelian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseData as $data)
                                    <tr>
                                        <td>
                                            {{ \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $data->transactions }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($data->total_qty, 0) }} pcs
                                        </td>
                                        <td class="text-right">
                                            <strong>Rp {{ number_format($data->total_purchases, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ $summaryStats['total_transactions'] }}</th>
                                        <th class="text-center">{{ number_format($summaryStats['total_qty'], 0) }} pcs</th>
                                        <th class="text-right"><strong>Rp {{ number_format($summaryStats['total_purchases'], 0, ',', '.') }}</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Tidak ada data pembelian untuk periode ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
