@extends('layouts.app')

@section('title', 'Detail Produk Paket')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('produk-paket.index') }}" style="color: #6B7280; text-decoration: none; display: flex; align-items: center; font-size: 14px; margin-bottom: 8px;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Daftar Paket
                    </a>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-layer-group" style="color: #8B5CF6; margin-right: 12px; opacity: 0.8;"></i> {{ $produkPaket->nama_paket }}
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Detail komposisi paket/platter</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('produk-siap-jual.create', ['produk_paket_id' => $produkPaket->id]) }}" class="btn" style="background-color: #DCFCE7; color: #166534; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 500;">
                        <i class="fas fa-link me-2"></i> Lanjut ke Produk Siap Jual
                    </a>
                    <a href="{{ route('produk-paket.edit', $produkPaket) }}" class="btn" style="background-color: #FEF3C7; color: #92400E; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 500;">
                        <i class="fas fa-pen me-2"></i> Edit Paket
                    </a>
                </div>
            </div>

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #D1FAE5; background-color: #F0FDF4;">
                    <i class="fas fa-check-circle" style="color: #22C55E; margin-right: 8px;"></i> <strong>Sukses!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- INFO CARD --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%);">
                        <div class="card-body text-center" style="padding: 24px;">
                            <i class="fas fa-calculator" style="font-size: 32px; color: white; opacity: 0.8; margin-bottom: 12px;"></i>
                            <h3 style="color: white; font-weight: 700; margin: 0;">Rp {{ number_format($produkPaket->hpp_total ?? 0, 0, ',', '.') }}</h3>
                            <small style="color: rgba(255,255,255,0.8);">HPP Total / Paket</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%);">
                        <div class="card-body text-center" style="padding: 24px;">
                            <i class="fas fa-list" style="font-size: 32px; color: white; opacity: 0.8; margin-bottom: 12px;"></i>
                            <h3 style="color: white; font-weight: 700; margin: 0;">{{ $produkPaket->details->count() }}</h3>
                            <small style="color: rgba(255,255,255,0.8);">Jumlah Item</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #22C55E 0%, #4ADE80 100%);">
                        <div class="card-body text-center" style="padding: 24px;">
                            <i class="fas fa-box" style="font-size: 32px; color: white; opacity: 0.8; margin-bottom: 12px;"></i>
                            <h3 style="color: white; font-weight: 700; margin: 0;">{{ $produkPaket->produkSiapJuals->count() }}</h3>
                            <small style="color: rgba(255,255,255,0.8);">Produk Siap Jual</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: white;">
                        <div class="card-body text-center" style="padding: 24px;">
                            @if($produkPaket->status === 'aktif')
                                <span style="background-color: #DCFCE7; color: #166534; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                </span>
                            @else
                                <span style="background-color: #FEE2E2; color: #B91C1C; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-times-circle me-1"></i> Nonaktif
                                </span>
                            @endif
                            <p style="color: #6B7280; margin: 12px 0 0 0; font-size: 12px;">Status Paket</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO PAKET --}}
            <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                    <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                        <i class="fas fa-info-circle" style="color: #8B5CF6; margin-right: 8px;"></i> Informasi Paket
                    </h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label style="color: #6B7280; font-size: 12px; text-transform: uppercase; font-weight: 600;">Nama Paket</label>
                            <p style="color: #1A1A1A; font-weight: 500; margin: 4px 0 0 0;">{{ $produkPaket->nama_paket }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label style="color: #6B7280; font-size: 12px; text-transform: uppercase; font-weight: 600;">Kode Paket</label>
                            <p style="color: #1A1A1A; font-weight: 500; margin: 4px 0 0 0;">{{ $produkPaket->kode_paket ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label style="color: #6B7280; font-size: 12px; text-transform: uppercase; font-weight: 600;">Dibuat</label>
                            <p style="color: #1A1A1A; font-weight: 500; margin: 4px 0 0 0;">{{ $produkPaket->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($produkPaket->deskripsi)
                        <div class="row">
                            <div class="col-md-12">
                                <label style="color: #6B7280; font-size: 12px; text-transform: uppercase; font-weight: 600;">Deskripsi</label>
                                <p style="color: #1A1A1A; margin: 4px 0 0 0;">{{ $produkPaket->deskripsi }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TABEL KOMPONEN --}}
            <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                    <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                        <i class="fas fa-list" style="color: #8B5CF6; margin-right: 8px;"></i> Komponen Paket ({{ $produkPaket->details->count() }} item)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" style="font-size: 14px;">
                            <thead>
                                <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px;">No</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px;">Nama Item</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px;">Kategori</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">Qty/Paket</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">HPP/PCS</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">HPP Item</th>
                                    <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">Stok Gudang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produkPaket->details as $index => $detail)
                                    @php
                                        $stockGudang = $detail->stockGudang;
                                        $hppPerPcs = $stockGudang ? (($stockGudang->harga_beli_pack ?? 0) / max(1, $stockGudang->konversi_satuan)) : 0;
                                        $hppItem = $detail->qty_per_paket * $hppPerPcs;
                                        $stokCukup = ($stockGudang->pcs_sisa ?? 0) >= $detail->qty_per_paket;
                                    @endphp
                                    <tr style="border-bottom: 1px solid #E5E7EB;">
                                        <td style="padding: 16px 20px; color: #6B7280;">{{ $index + 1 }}</td>
                                        <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                            {{ $stockGudang->nama_produk ?? 'Item tidak ditemukan' }}
                                            @if($detail->keterangan)
                                                <br><small style="color: #9CA3AF;">{{ $detail->keterangan }}</small>
                                            @endif
                                        </td>
                                        <td style="padding: 16px 20px;">
                                            <span style="background-color: #E8E8FF; color: #5B21B6; padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                                                {{ $stockGudang->category->nama_kategori ?? '-' }}
                                            </span>
                                        </td>
                                        <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">{{ number_format($detail->qty_per_paket, 2) }} PCS</td>
                                        <td style="padding: 16px 20px; text-align: right; color: #6B7280;">Rp {{ number_format($hppPerPcs, 0, ',', '.') }}</td>
                                        <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">Rp {{ number_format($hppItem, 0, ',', '.') }}</td>
                                        <td style="padding: 16px 20px; text-align: right;">
                                            @if($stokCukup)
                                                <span style="background-color: #DCFCE7; color: #166534; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ number_format($stockGudang->pcs_sisa ?? 0, 0) }} PCS
                                                </span>
                                            @else
                                                <span style="background-color: #FEE2E2; color: #B91C1C; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ number_format($stockGudang->pcs_sisa ?? 0, 0) }} PCS
                                                    <i class="fas fa-exclamation-triangle ms-1"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #F0F9FF; border-top: 2px solid #8B5CF6;">
                                    <td colspan="5" style="padding: 16px 20px; text-align: right; font-weight: 600; color: #1A1A1A;">Total HPP Paket:</td>
                                    <td style="padding: 16px 20px; text-align: right; font-weight: 700; color: #8B5CF6; font-size: 16px;">Rp {{ number_format($produkPaket->hpp_total ?? 0, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PRODUK SIAP JUAL YANG MENGGUNAKAN PAKET INI --}}
            @if($produkPaket->produkSiapJuals->count() > 0)
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                    <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                        <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                            <i class="fas fa-box" style="color: #22C55E; margin-right: 8px;"></i> Produk Siap Jual ({{ $produkPaket->produkSiapJuals->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px;">Nama Produk</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">Harga Jual</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: right;">Stok Siap Jual</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produkPaket->produkSiapJuals as $produk)
                                        <tr style="border-bottom: 1px solid #E5E7EB;">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">{{ $produk->nama_produk ?? '-' }}</td>
                                            <td style="padding: 16px 20px; text-align: right; color: #EF4444; font-weight: 600;">Rp {{ number_format($produk->harga_jual ?? 0, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px; text-align: right;">
                                                <span style="background-color: #E0F2FE; color: #0369A1; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ $produk->stok_siap_jual ?? 0 }} paket
                                                </span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <a href="{{ route('produk-siap-jual.show', $produk) }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0284C7; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px;">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
