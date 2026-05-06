@extends('layouts.app')

@section('title', 'Produk Terlaris - Laporan')

@section('content')
<div class="container-fluid py-5" style="background-color: #F5F7FA; min-height: 100vh;">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: #1A1A1A; font-weight: 700; font-size: 28px;">
                        <i class="fas fa-chart-bar" style="margin-right: 12px;"></i> Produk Terlaris
                    </h1>
                    <p style="color: #6B7280; font-size: 14px; margin: 0;">Laporan penjualan berdasarkan data transaksi yang sudah lunas</p>
                </div>
                <button class="btn" onclick="window.print()" style="border: 1px solid #D1D5DB; color: #6B7280; background: white; border-radius: 8px; padding: 8px 16px; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#9CA3AF'; this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.borderColor='#D1D5DB'; this.style.backgroundColor='white';">
                    <i class="fas fa-print" style="margin-right: 6px;"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-0">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                                        <th width="60" class="py-4 ps-4 pe-3" style="border: none; color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fas fa-hashtag"></i> No.
                                        </th>
                                        <th class="py-4 px-3" style="border: none; color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Nama Produk
                                        </th>
                                        <th width="180" class="py-4 px-3 text-center" style="border: none; color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fas fa-box"></i> Total Terjual
                                        </th>
                                        <th width="160" class="py-4 px-3 text-center" style="border: none; color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fas fa-receipt"></i> Transaksi
                                        </th>
                                        <th width="180" class="py-4 px-3 text-center" style="border: none; color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fas fa-calculator"></i> Rata-rata
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $index => $item)
                                    <tr class="product-row" style="border-bottom: 1px solid #E5E7EB; transition: background-color 0.2s ease;">
                                        <td class="ps-4 pe-3 py-4" style="border: none; vertical-align: middle;">
                                            <span class="badge" style="background-color: #6366F1; color: white; font-weight: 700; font-size: 13px; padding: 6px 10px; border-radius: 6px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4" style="border: none; vertical-align: middle;">
                                            <strong style="color: #1F2937; font-size: 14px; display: block;">
                                                @if($item->produk && $item->produk->stockGudang)
                                                    {{ $item->produk->stockGudang->nama_produk }}
                                                @elseif($item->produk)
                                                    {{ $item->produk->nama_produk ?? 'Produk Tidak Tersedia' }}
                                                @else
                                                    <span style="color: #9CA3AF; font-weight: 500;">Produk Dihapus</span>
                                                @endif
                                            </strong>
                                        </td>
                                        <td class="px-3 py-4 text-center" style="border: none; vertical-align: middle;">
                                            <span style="background-color: #DCFCE7; color: #166534; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-block;">
                                                {{ number_format($item->total_qty, 0) }} pcs
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-center" style="border: none; vertical-align: middle;">
                                            <span style="background-color: #E0F2FE; color: #0369A1; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-block;">
                                                {{ $item->total_transactions }} <span style="font-weight: 500;">kali</span>
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-center" style="border: none; vertical-align: middle;">
                                            <span style="background-color: #FFEDD5; color: #9A3412; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-block;">
                                                {{ number_format($item->total_qty / $item->total_transactions, 2) }} pcs
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center py-5 m-0" style="border: none; background-color: #E0F2FE; color: #0369A1; border-radius: 0; margin: 0;">
                            <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 12px; opacity: 0.6;"></i>
                            <p style="font-size: 15px; font-weight: 500; margin: 0;">
                                Belum ada data penjualan dengan status lunas
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    @if($topProducts->count() > 0)
    <div class="row">
        <!-- Card 1: Total Produk Terlaris -->
        <div class="col-md-4 mb-4">
            <div class="card border-0" style="background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-flex-start">
                        <div>
                            <p style="color: #6B7280; font-size: 13px; font-weight: 500; margin: 0 0 8px 0;">
                                <i class="fas fa-cubes" style="margin-right: 6px; opacity: 0.7;"></i> TOTAL PRODUK TERLARIS
                            </p>
                            <h2 style="color: #6366F1; font-weight: 700; font-size: 32px; margin: 0; line-height: 1;">
                                {{ $totalProducts }}
                            </h2>
                        </div>
                        <i class="fas fa-chart-pie" style="font-size: 48px; color: #6366F1; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Penjualan (PCS) -->
        <div class="col-md-4 mb-4">
            <div class="card border-0" style="background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-flex-start">
                        <div>
                            <p style="color: #6B7280; font-size: 13px; font-weight: 500; margin: 0 0 8px 0;">
                                <i class="fas fa-box-open" style="margin-right: 6px; opacity: 0.7;"></i> TOTAL PENJUALAN (PCS)
                            </p>
                            <h2 style="color: #10B981; font-weight: 700; font-size: 32px; margin: 0; line-height: 1;">
                                {{ number_format($totalPcs, 0) }}
                            </h2>
                            <p style="color: #9CA3AF; font-size: 12px; margin: 6px 0 0 0;">piece/pcs</p>
                        </div>
                        <i class="fas fa-boxes" style="font-size: 48px; color: #10B981; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Transaksi -->
        <div class="col-md-4 mb-4">
            <div class="card border-0" style="background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-flex-start">
                        <div>
                            <p style="color: #6B7280; font-size: 13px; font-weight: 500; margin: 0 0 8px 0;">
                                <i class="fas fa-receipt" style="margin-right: 6px; opacity: 0.7;"></i> TOTAL TRANSAKSI
                            </p>
                            <h2 style="color: #0369A1; font-weight: 700; font-size: 32px; margin: 0; line-height: 1;">
                                {{ number_format($totalTransactions, 0) }}
                            </h2>
                            <p style="color: #9CA3AF; font-size: 12px; margin: 6px 0 0 0;">transaksi</p>
                        </div>
                        <i class="fas fa-chart-line" style="font-size: 48px; color: #0369A1; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Inline Styles untuk Hover & Print -->
<style>
    /* Hover effect untuk baris tabel */
    .product-row:hover {
        background-color: #F3F4F6 !important;
        cursor: pointer;
    }

    /* Print Stylesheet */
    @media print {
        body {
            background-color: white !important;
        }
        
        .btn, button {
            display: none !important;
        }
        
        .container-fluid {
            background-color: white !important;
            padding: 0 !important;
        }
        
        .card {
            box-shadow: none !important;
            break-inside: avoid;
        }
        
        .table {
            page-break-inside: avoid;
        }
        
        .row {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
