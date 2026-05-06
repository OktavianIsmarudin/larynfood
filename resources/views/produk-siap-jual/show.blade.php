@extends('layouts.app')

@section('title', 'Detail Produk Siap Jual')

@section('content')
<div class="container-fluid py-4" style="background-color: #F5F7FA; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-9 mx-auto">

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Berhasil!</h5>
                    <p class="mb-0">{{ $message }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- HEADER CARD --}}
            <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)';">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-2 fw-bold" style="font-size: 28px; color: #1A1A1A;">
                                @if($produkSiapJual->isPaket())
                                    <span style="background-color: #E8E8FF; color: #5B21B6; padding: 4px 10px; border-radius: 6px; font-size: 14px; font-weight: 600; margin-right: 8px;">PAKET</span>
                                @endif
                                {{ $produkSiapJual->nama_produk }}
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 13px; color: #65676B;">
                                @if($produkSiapJual->isPaket())
                                    <i class="fas fa-layer-group"></i> 
                                    Dari: <strong style="color: #5B21B6;">{{ $produkSiapJual->produkPaket->nama_paket ?? 'N/A' }}</strong>
                                    ({{ $produkSiapJual->produkPaket->details->count() ?? 0 }} item komponen)
                                @else
                                    <i class="fas fa-box"></i> 
                                    Dari: <strong style="color: #1A1A1A;">{{ $produkSiapJual->stockGudang->nama_produk ?? 'N/A' }}</strong> 
                                    (SKU: {{ $produkSiapJual->stockGudang->sku ?? '-' }})
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            <small style="color: #65676B; display: block; font-size: 12px;">Dibuat: {{ $produkSiapJual->created_at->format('d M Y') }}</small>
                            <small style="color: #65676B; font-size: 12px;">Diubah: {{ $produkSiapJual->updated_at->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMASI DASAR (2 KOLOM) --}}
            <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; height: 100%;">
                        <div class="card-body p-4">
                            <h6 class="mb-3 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-info-circle" style="opacity: 0.6; margin-right: 8px;"></i> Informasi Dasar
                            </h6>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">PCS per Paket</small>
                                <p class="mb-0 fw-bold text-info fs-5" style="color: #0084FF;">{{ $produkSiapJual->pcs_per_paket ?? 'N/A' }} PCS</p>
                            </div>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">HPP per PCS</small>
                                <p class="mb-0 fw-bold fs-5" style="color: #F57C00;">Rp {{ number_format($produkSiapJual->hpp_per_pcs, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Margin Laba</small>
                                <p class="mb-0 fw-bold text-success fs-5" style="color: #2E7D32;">{{ $produkSiapJual->margin_laba }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; height: 100%;">
                        <div class="card-body p-4">
                            <h6 class="mb-3 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-money-bill-wave" style="opacity: 0.6; margin-right: 8px;"></i> Harga Jual
                            </h6>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Harga Jual per PCS</small>
                                <p class="mb-0 fw-bold fs-5" style="color: #2E7D32;">Rp {{ number_format($produkSiapJual->harga_jual_per_pcs, 0, ',', '.') }}</p>
                            </div>
                            
                            <div>
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Harga Jual per Paket</small>
                                <p class="mb-0 fw-bold fs-5" style="color: #2E7D32;">Rp {{ number_format($produkSiapJual->harga_jual_per_paket, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STOCK INFORMATION (2 KOLOM) --}}
            <div class="row mb-4">
            {{-- STOCK INFORMATION (2 KOLOM) --}}
            <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="background-color: #E3F2FF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 12px; height: 100%;">
                        <div class="card-header border-0 p-5" style="background-color: transparent;">
                            <h6 class="mb-0 fw-bold" style="color: #0052CC; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                @if($produkSiapJual->isPaket())
                                    <i class="fas fa-layer-group" style="opacity: 0.7; margin-right: 8px;"></i> Komponen Paket
                                @else
                                    <i class="fas fa-inbox" style="opacity: 0.7; margin-right: 8px;"></i> Stok Gudang
                                @endif
                            </h6>
                        </div>
                        <div class="card-body p-5 pt-2">
                            @if($produkSiapJual->isPaket() && $produkSiapJual->produkPaket)
                                {{-- Tampilkan komponen paket --}}
                                <div class="mb-3">
                                    <small style="color: #6B7280; font-size: 13px;">HPP Total Paket</small>
                                    <h4 style="color: #0052CC; font-weight: 700; margin: 4px 0;">Rp {{ number_format($produkSiapJual->produkPaket->hpp_total ?? 0, 0, ',', '.') }}</h4>
                                </div>
                                <hr style="border-color: #A3C4F3;">
                                <small style="color: #6B7280; display: block; margin-bottom: 8px; font-size: 12px;">Komponen:</small>
                                @foreach($produkSiapJual->produkPaket->details as $detail)
                                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
                                        <span style="color: #1A1A1A;">{{ $detail->stockGudang->nama_produk ?? 'N/A' }}</span>
                                        <span style="color: #0052CC; font-weight: 500;">{{ $detail->qty_per_paket }} pcs (sisa: {{ $detail->stockGudang->pcs_sisa ?? 0 }})</span>
                                    </div>
                                @endforeach
                            @else
                                {{-- Tampilkan stok gudang tunggal --}}
                                <div class="d-flex align-items-flex-start justify-content-between">
                                    <div style="flex: 1;">
                                        <h3 style="color: #0052CC; font-weight: 700; margin: 0 0 8px 0; font-size: 38px; line-height: 1.1;">{{ $produkSiapJual->stockGudang->pcs_sisa ?? 0 }}</h3>
                                        <small style="color: #6B7280; display: block; font-size: 13px;">PCS tersedia di gudang</small>
                                    </div>
                                    <div style="text-align: right;">
                                        <i class="fas fa-inbox" style="font-size: 56px; color: #3B82F6; opacity: 0.15;"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="background-color: #E8F5E9; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 12px; height: 100%;">
                        <div class="card-header border-0 p-5" style="background-color: transparent;">
                            <h6 class="mb-0 fw-bold" style="color: #00AA00; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-shopping-bag" style="opacity: 0.7; margin-right: 8px;"></i> Stok Siap Jual
                            </h6>
                        </div>
                        <div class="card-body p-5 pt-2">
                            <div class="d-flex align-items-flex-start justify-content-between">
                                <div style="flex: 1;">
                                    <h3 style="color: #00AA00; font-weight: 700; margin: 0 0 8px 0; font-size: 38px; line-height: 1.1;">
                                        {{ ($produkSiapJual->stok_siap_jual ?? 0) * ($produkSiapJual->pcs_per_paket ?? 1) }}
                                    </h3>
                                    <small style="color: #6B7280; display: block; font-size: 13px;">PCS siap untuk dijual</small>
                                    @if(($produkSiapJual->pcs_per_paket ?? 1) > 1)
                                        <small style="color: #9CA3AF; display: block; font-size: 12px; margin-top: 4px;">
                                            ({{ $produkSiapJual->stok_siap_jual ?? 0 }} paket × {{ $produkSiapJual->pcs_per_paket ?? 1 }} pcs)
                                        </small>
                                    @endif
                                </div>
                                <div style="text-align: right;">
                                    <i class="fas fa-shopping-bag" style="font-size: 56px; color: #22C55E; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAMBAH STOK SECTION --}}
            @if($produkSiapJual->isPaket())
                {{-- PAKET: Tambah stok tanpa mengurangi gudang --}}
                @php
                    $cekStok = $produkSiapJual->produkPaket ? $produkSiapJual->produkPaket->cekStokCukup(1) : ['sufficient' => false];
                @endphp
                @if($cekStok['sufficient'])
                    <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; border-left: 4px solid #5B21B6;">
                        <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                            <h6 class="mb-0 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-plus-circle" style="opacity: 0.8; margin-right: 8px; color: #5B21B6;"></i> Tambah Stok Siap Jual (Paket)
                            </h6>
                        </div>
                        <div class="card-body p-4" style="border-top: 1px solid #E5E7EB;">
                            <div class="alert mb-3" style="background-color: #F3E8FF; color: #5B21B6; border: 1px solid #C4B5FD; border-radius: 8px;">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    Untuk produk paket, stok komponen akan dikurangi <strong>saat penjualan</strong>, bukan saat tambah stok siap jual.
                                </small>
                            </div>
                            <form action="{{ route('produk-siap-jual.tambah-stock', $produkSiapJual) }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <label for="jumlah_paket" class="form-label fw-bold" style="color: #1A1A1A; font-size: 14px;">Tambah Berapa Paket?</label>
                                        <div class="input-group">
                                            <input type="number" id="jumlah_paket" name="jumlah_paket" class="form-control" 
                                                   placeholder="Contoh: 2" min="1" 
                                                   value="1"
                                                   style="border-color: #D1D5DB; border-radius: 6px 0 0 6px;"
                                                   required>
                                            <button type="submit" class="btn fw-bold" style="background-color: #5B21B6; color: white; border: none; border-radius: 0 6px 6px 0;">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                        <small style="color: #65676B; margin-top: 8px; display: block; font-size: 12px;">
                                            Stok komponen akan dicek saat penjualan
                                        </small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert mb-4" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D; border-radius: 8px;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Stok komponen tidak mencukupi!</strong> 
                        Pastikan semua komponen paket memiliki stok yang cukup.
                    </div>
                @endif
            @elseif ($produkSiapJual->stockGudang && $produkSiapJual->stockGudang->pcs_sisa > 0)
                <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; border-left: 4px solid #2E7D32;">
                    <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                        <h6 class="mb-0 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                            <i class="fas fa-plus-circle" style="opacity: 0.8; margin-right: 8px; color: #2E7D32;"></i> Tambah Stok Siap Jual
                        </h6>
                    </div>
                    <div class="card-body p-4" style="border-top: 1px solid #E5E7EB;">
                        <form action="{{ route('produk-siap-jual.tambah-stock', $produkSiapJual) }}" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="jumlah_paket" class="form-label fw-bold" style="color: #1A1A1A; font-size: 14px;">Tambah Berapa Paket?</label>
                                    <div class="input-group">
                                        <input type="number" id="jumlah_paket" name="jumlah_paket" class="form-control" 
                                               placeholder="Contoh: 2" min="1" max="{{ floor(($produkSiapJual->stockGudang->pcs_sisa ?? 0) / ($produkSiapJual->pcs_per_paket ?? 1)) }}" 
                                               value="1"
                                               style="border-color: #D1D5DB; border-radius: 6px 0 0 6px;"
                                               required>
                                        <button type="submit" class="btn fw-bold" style="background-color: #2E7D32; color: white; border: none; border-radius: 0 6px 6px 0;">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                    <small style="color: #65676B; margin-top: 8px; display: block; font-size: 12px;">
                                        Max: {{ floor(($produkSiapJual->stockGudang->pcs_sisa ?? 0) / ($produkSiapJual->pcs_per_paket ?? 1)) }} paket 
                                        ({{ floor(($produkSiapJual->stockGudang->pcs_sisa ?? 0) / ($produkSiapJual->pcs_per_paket ?? 1)) * ($produkSiapJual->pcs_per_paket ?? 1) }} PCS)
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert mb-4" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D; border-radius: 8px;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Stock gudang habis!</strong> Tidak bisa menambah stok siap jual saat ini.
                </div>
            @endif

            {{-- PERHITUNGAN HARGA SECTION --}}
            <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; background-color: #FAFBFC;">
                <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                    <h6 class="mb-0 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                        <i class="fas fa-calculator" style="opacity: 0.6; margin-right: 8px; color: #0084FF;"></i> Rincian Biaya & Harga
                    </h6>
                </div>
                <div class="card-body p-4" style="border-top: 1px solid #E5E7EB;">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px;">HPP Total per PCS</small>
                            <p class="fw-bold fs-5 mb-0" style="color: #F57C00;">
                                Rp {{ number_format($produkSiapJual->hpp_total_per_pcs ?? 0, 0, ',', '.') }}
                            </p>
                            <small style="color: #65676B; font-size: 12px;">= HPP + Biaya Lain-lain</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px;">HPP Total per Paket</small>
                            <p class="fw-bold fs-5 mb-0" style="color: #F57C00;">
                                Rp {{ number_format(($produkSiapJual->hpp_total_per_pcs ?? 0) * ($produkSiapJual->pcs_per_paket ?? 1), 0, ',', '.') }}
                            </p>
                            <small style="color: #65676B; font-size: 12px;">= HPP Total/PCS × {{ $produkSiapJual->pcs_per_paket }} PCS</small>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #E5E7EB;">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600;"><strong>Biaya Lain-lain (per Paket)</strong></small>
                            <ul class="small mb-2" style="color: #65676B; padding-left: 18px;">
                                <li>Packing: Rp {{ number_format($produkSiapJual->biaya_packing ?? 0, 0, ',', '.') }}</li>
                                <li>Saos: Rp {{ number_format($produkSiapJual->biaya_saos ?? 0, 0, ',', '.') }}</li>
                                <li>Sumpit: Rp {{ number_format($produkSiapJual->biaya_sumpit ?? 0, 0, ',', '.') }}</li>
                                <li>Tenaga: Rp {{ number_format($produkSiapJual->biaya_tenaga ?? 0, 0, ',', '.') }}</li>
                            </ul>
                            <p class="fw-bold mb-0" style="color: #F57C00;">Total: Rp {{ number_format($produkSiapJual->total_biaya_lain ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600;"><strong>Margin Laba</strong></small>
                            <p class="fw-bold fs-5 mb-3" style="color: #2E7D32;">{{ $produkSiapJual->margin_laba }}%</p>
                            <small style="color: #65676B; font-size: 12px;">
                                <i class="fas fa-info-circle" style="opacity: 0.6;"></i> HPP dan margin sudah terkunci sejak pembuatan
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PEMAKAIAN PERALATAN SECTION --}}
            <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; border-left: 4px solid #F57C00;">
                <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                    <h6 class="mb-0 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                        <i class="fas fa-tools" style="opacity: 0.8; margin-right: 8px; color: #F57C00;"></i> Pemakaian Peralatan / Kemasan
                    </h6>
                </div>
                <div class="card-body p-4" style="border-top: 1px solid #E5E7EB;">
                    <div class="alert mb-4" style="background-color: #E8F4FD; color: #0084FF; border: 1px solid #B3D9E8; border-radius: 8px;">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Peralatan yang dipilih akan <strong>mengurangi stok gudang</strong> dan dicatat untuk kebutuhan internal. 
                            Tidak mempengaruhi penjualan atau omzet.
                        </small>
                    </div>

                    {{-- FORM TAMBAH PERALATAN --}}
                    <form id="equipmentForm" action="{{ route('produk-siap-jual.process-equipment', $produkSiapJual) }}" method="POST" class="mb-4">
                        @csrf
                        <div id="equipmentContainer">
                            <div class="equipment-row mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="peralatan_0" class="form-label fw-bold" style="color: #1A1A1A; font-size: 13px;">Pilih Peralatan / Kemasan</label>
                                        <select name="peralatan[0]" class="form-select equipment-select" data-index="0" 
                                                style="border-color: #D1D5DB; border-radius: 6px;"
                                                required>
                                            <option value="">-- Pilih Peralatan --</option>
                                        </select>
                                        <small class="text-muted" style="color: #65676B; font-size: 12px;">
                                            <span class="stock-info" data-index="0"></span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="qty_0" class="form-label fw-bold" style="color: #1A1A1A; font-size: 13px;">Jumlah (PCS)</label>
                                        <input type="number" name="peralatan_qty[0]" class="form-control jumlah-input" data-index="0" min="1" 
                                               placeholder="Contoh: 10"
                                               style="border-color: #D1D5DB; border-radius: 6px;">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end mb-2">
                                        <button type="button" class="btn btn-sm btn-remove-equipment" data-index="0" 
                                                style="display: none; background-color: #DC3545; color: white; border: none; border-radius: 6px;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" id="addEquipmentBtn" class="btn btn-sm fw-bold" 
                                    style="background-color: transparent; color: #F57C00; border: 1px solid #F57C00; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Tambah Peralatan Lain
                            </button>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn fw-bold" style="background-color: #F57C00; color: white; border: none; border-radius: 6px;">
                                <i class="fas fa-save"></i> Simpan Pemakaian
                            </button>
                            <button type="button" class="btn fw-bold" id="resetForm" style="background-color: #E5E7EB; color: #1A1A1A; border: none; border-radius: 6px;">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>

                    {{-- RIWAYAT PEMAKAIAN PERALATAN --}}
                    @if ($produkSiapJual->pemakaianPeralatan->count() > 0)
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-history" style="opacity: 0.6; margin-right: 8px;"></i> Riwayat Pemakaian
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm" style="border-collapse: collapse;">
                                    <thead style="background-color: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                                        <tr>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: left;">#</th>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: left;">Peralatan</th>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: left;">Kategori</th>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: right;">Jumlah</th>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: left;">Tanggal</th>
                                            <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: left;">User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produkSiapJual->pemakaianPeralatan as $key => $pemakaian)
                                            <tr style="border-bottom: 1px solid #E5E7EB;">
                                                <td style="color: #65676B; font-size: 12px; padding: 8px 12px;">{{ $key + 1 }}</td>
                                                <td style="padding: 8px 12px;">
                                                    <strong style="color: #1A1A1A;">{{ $pemakaian->getNamaPeralatan() }}</strong>
                                                    <br>
                                                    <small style="color: #65676B; font-size: 11px;">SKU: {{ $pemakaian->stockGudang?->sku ?? '-' }}</small>
                                                </td>
                                                <td style="padding: 8px 12px;">
                                                    <span class="badge" style="background-color: #E8F4FD; color: #0084FF; font-size: 11px;">{{ $pemakaian->getKategori() }}</span>
                                                </td>
                                                <td style="color: #1A1A1A; font-weight: 600; font-size: 12px; padding: 8px 12px; text-align: right;">{{ $pemakaian->jumlah_pakai }} PCS</td>
                                                <td style="color: #65676B; font-size: 12px; padding: 8px 12px;">{{ $pemakaian->getTanggalFormat() }}</td>
                                                <td style="color: #65676B; font-size: 12px; padding: 8px 12px;">{{ $pemakaian->user?->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert mb-0" style="background-color: #F9FAFB; color: #65676B; border: 1px solid #E5E7EB; border-radius: 8px; text-align: center;">
                            <small>Belum ada riwayat pemakaian peralatan</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('produk-siap-jual.edit', $produkSiapJual) }}" class="btn fw-bold" style="background-color: #F57C00; color: white; border: none; border-radius: 6px;">
                    <i class="fas fa-edit"></i> Edit Produk
                </a>
                <form id="deleteForm" action="{{ route('produk-siap-jual.destroy', $produkSiapJual) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn fw-bold" style="background-color: #DC3545; color: white; border: none; border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                <a href="{{ route('produk-siap-jual.index') }}" class="btn fw-bold ms-auto" style="background-color: #E5E7EB; color: #1A1A1A; border: none; border-radius: 6px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    .form-label {
        font-size: 14px;
        font-weight: 500;
        color: #1A1A1A;
    }
    
    .form-control, .form-select {
        border-color: #D1D5DB;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0084FF;
        box-shadow: 0 0 0 3px rgba(0, 132, 255, 0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let equipmentCount = 1;
    const availableEquipment = [];

    // Fetch available equipment on page load
    fetchAvailableEquipment();

    function fetchAvailableEquipment() {
        fetch('{{ route("produk-siap-jual.available-equipment") }}')
            .then(response => response.json())
            .then(result => {
                const data = result.success ? result.data : result;
                availableEquipment.length = 0;
                availableEquipment.push(...data);
                populateEquipmentSelects();
            })
            .catch(error => {
                console.error('Error fetching equipment:', error);
                showErrorAlert('Gagal memuat data peralatan. Silakan refresh halaman.');
            });
    }

    function showErrorAlert(message) {
        const alertHtml = `
            <div class="alert mb-3" style="background-color: #FFEBEE; color: #C62828; border: 1px solid #EF5350; border-radius: 8px;">
                <i class="fas fa-exclamation-circle"></i> ${message}
            </div>
        `;
        const container = document.querySelector('.card-body');
        if (container) {
            container.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }

    function populateEquipmentSelects() {
        document.querySelectorAll('.equipment-select').forEach(select => {
            const selectedValue = select.value;
            select.innerHTML = '<option value="">-- Pilih Peralatan --</option>';
            
            availableEquipment.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = `${item.nama_produk} (${item.pcs_sisa} PCS)`;
                option.dataset.stock = item.pcs_sisa;
                select.appendChild(option);
            });
            
            if (selectedValue) {
                select.value = selectedValue;
            }
        });
    }

    // Handle equipment selection change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('equipment-select')) {
            const index = e.target.dataset.index;
            const selectedOption = e.target.options[e.target.selectedIndex];
            const stockInfo = document.querySelector(`.stock-info[data-index="${index}"]`);
            const jumlahInput = document.querySelector(`.jumlah-input[data-index="${index}"]`);
            
            if (selectedOption.value) {
                const stock = selectedOption.dataset.stock;
                stockInfo.textContent = `Stok: ${stock} PCS`;
                jumlahInput.max = stock;
                jumlahInput.required = true;
            } else {
                stockInfo.textContent = '';
                jumlahInput.max = '';
                jumlahInput.value = '';
                jumlahInput.required = false;
            }
        }
    });

    // Add equipment row
    document.getElementById('addEquipmentBtn').addEventListener('click', function() {
        const container = document.getElementById('equipmentContainer');
        const newRow = document.createElement('div');
        newRow.className = 'equipment-row mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="peralatan_${equipmentCount}" class="form-label fw-bold" style="color: #1A1A1A; font-size: 13px;">Pilih Peralatan / Kemasan</label>
                    <select name="peralatan[${equipmentCount}]" class="form-select equipment-select" data-index="${equipmentCount}" style="border-color: #D1D5DB; border-radius: 6px;">
                        <option value="">-- Pilih Peralatan --</option>
                    </select>
                    <small style="color: #65676B; font-size: 12px;">
                        <span class="stock-info" data-index="${equipmentCount}"></span>
                    </small>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="qty_${equipmentCount}" class="form-label fw-bold" style="color: #1A1A1A; font-size: 13px;">Jumlah (PCS)</label>
                    <input type="number" name="peralatan_qty[${equipmentCount}]" class="form-control jumlah-input" data-index="${equipmentCount}" min="1" placeholder="Contoh: 10" style="border-color: #D1D5DB; border-radius: 6px;">
                </div>
                <div class="col-md-2 d-flex align-items-end mb-2">
                    <button type="button" class="btn btn-sm btn-remove-equipment" data-index="${equipmentCount}" style="background-color: #DC3545; color: white; border: none; border-radius: 6px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        
        const newSelect = newRow.querySelector('.equipment-select');
        newSelect.innerHTML = '<option value="">-- Pilih Peralatan --</option>';
        availableEquipment.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.nama_produk} (${item.pcs_sisa} PCS)`;
            option.dataset.stock = item.pcs_sisa;
            newSelect.appendChild(option);
        });
        
        equipmentCount++;
        updateRemoveButtons();
    });

    // Remove equipment row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-equipment')) {
            e.target.closest('.equipment-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.equipment-row');
        rows.forEach(row => {
            const removeBtn = row.querySelector('.btn-remove-equipment');
            removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
        });
    }

    // Form submission
    document.getElementById('equipmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const rows = document.querySelectorAll('.equipment-row');
        let hasError = false;

        rows.forEach(row => {
            const select = row.querySelector('.equipment-select');
            const jumlahInput = row.querySelector('.jumlah-input');
            
            if (select.value && !jumlahInput.value) {
                jumlahInput.classList.add('is-invalid');
                hasError = true;
            } else if (select.value) {
                jumlahInput.classList.remove('is-invalid');
            }
        });

        if (hasError) {
            alert('Mohon isi jumlah untuk setiap peralatan yang dipilih');
            return;
        }

        if (document.querySelectorAll('.equipment-select').length === 0 || !Array.from(rows).some(r => r.querySelector('.equipment-select').value)) {
            alert('Mohon pilih minimal satu peralatan');
            return;
        }

        this.submit();
    });

    // Reset form
    document.getElementById('resetForm').addEventListener('click', function() {
        document.getElementById('equipmentForm').reset();
        document.querySelectorAll('.stock-info').forEach(el => el.textContent = '');
        document.querySelectorAll('.jumlah-input').forEach(el => el.value = '');
    });

    // Initial setup
    updateRemoveButtons();
});
</script>
@endsection
