@extends('layouts.app')

@section('title', 'Detail Stock Gudang')

@section('content')
<div class="container-fluid py-4" style="background-color: #F5F7FA; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-9 mx-auto">
            
            {{-- ALERTS --}}
            @php
                $pcs_sisa = $stockGudang->pcs_sisa ?? (($stockGudang->jumlah_pack ?? $stockGudang->jumlah_stock) * ($stockGudang->konversi_satuan ?? 1));
            @endphp
            
            @if ($pcs_sisa == 0)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Stock Habis!</h5>
                    <p class="mb-0">{{ $stockGudang->nama_produk }} tidak memiliki stock tersisa. Silakan lakukan pembelian baru.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif ($pcs_sisa < (($stockGudang->total_pcs ?? (($stockGudang->jumlah_pack ?? $stockGudang->jumlah_stock) * ($stockGudang->konversi_satuan ?? 1))) * 0.2))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Stock Hampir Habis!</h5>
                    <p class="mb-0">Stock di bawah 20%. Segera lakukan pembelian stock baru.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- HEADER CARD --}}
            <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)';">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-2 fw-bold" style="font-size: 28px; color: #1A1A1A;">{{ $stockGudang->nama_produk }}</h3>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge" style="background-color: #F0F2F5; color: #65676B; font-weight: 500;">{{ $stockGudang->sku }}</span>
                                @if ($stockGudang->purchase_id)
                                    <span class="badge" style="background-color: #E8F5E9; color: #2E7D32;">
                                        <i class="fas fa-receipt"></i> Dari Pembelian
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #F3E5F5; color: #6A1B9A;">
                                        <i class="fas fa-edit"></i> Input Manual
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <small style="color: #65676B; display: block; font-size: 12px;">Dibuat: {{ $stockGudang->created_at->format('d M Y') }}</small>
                            <small style="color: #65676B; font-size: 12px;">Diubah: {{ $stockGudang->updated_at->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMASI UTAMA (2 KOLOM) --}}
            <div class="row mb-4">
                {{-- KOLOM KIRI --}}
                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; height: 100%;">
                        <div class="card-body p-4">
                            <h6 class="mb-3 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-folder" style="opacity: 0.6; margin-right: 8px;"></i> Kategori & Jenis
                            </h6>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Kategori</small>
                                <p class="mb-0 fw-bold" style="color: #1A1A1A;">{{ $stockGudang->category->nama_kategori ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Jenis Kategori</small>
                                <span class="badge p-2" style="background-color: {{ $stockGudang->getJenisKategori() === 'peralatan' ? '#FFF3E0' : '#E8F5E9' }}; color: {{ $stockGudang->getJenisKategori() === 'peralatan' ? '#E65100' : '#2E7D32' }}; font-size: 13px;">
                                    <i class="{{ $stockGudang->getJenisKategoriIcon() }}"></i>
                                    {{ $stockGudang->getJenisKategoriLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-lg-6 mb-4">
                    <div class="card border-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; height: 100%;">
                        <div class="card-body p-4">
                            <h6 class="mb-3 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                                <i class="fas fa-info-circle" style="opacity: 0.6; margin-right: 8px;"></i> Informasi Umum
                            </h6>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Supplier</small>
                                <p class="mb-0 fw-bold" style="color: #1A1A1A;">{{ $stockGudang->supplier->nama_supplier ?? '-' }}</p>
                            </div>
                            
                            <div class="mb-4" style="padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Lokasi Gudang</small>
                                <p class="mb-0 fw-bold" style="color: #1A1A1A;">{{ $stockGudang->lokasi_gudang ?? $stockGudang->gudang_penyimpanan ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <small style="color: #65676B; display: block; margin-bottom: 6px; font-size: 12px;">Konversi Satuan</small>
                                <p class="mb-0 fw-bold" style="color: #1A1A1A;">1 Pack = {{ $stockGudang->konversi_satuan ?? 1 }} {{ $stockGudang->satuan ?? $stockGudang->satuan_utama }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN STOK (GRID) --}}
            @php
                $pcsAwal = $stockGudang->pcs_awal ?? (($stockGudang->jumlah_pack ?? $stockGudang->jumlah_stock) * ($stockGudang->konversi_satuan ?? 1));
                $pcsSisa = $stockGudang->pcs_sisa ?? $pcsAwal;
                $pcsTerpakai = max(0, $pcsAwal - $pcsSisa);
            @endphp
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0" style="background-color: #E3F2FF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-flex-start justify-content-between">
                                <div style="flex: 1;">
                                    <small style="color: #0052CC; display: block; margin-bottom: 12px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-inbox" style="opacity: 0.7; margin-right: 6px;"></i> PCS Awal
                                    </small>
                                    <h2 style="color: #0052CC; font-weight: 700; margin: 0; font-size: 40px; line-height: 1.1;">
                                        {{ $pcsAwal }}
                                    </h2>
                                </div>
                                <div style="text-align: right;">
                                    <i class="fas fa-inbox" style="font-size: 56px; color: #3B82F6; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-0" style="background-color: #FFF0E0; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-flex-start justify-content-between">
                                <div style="flex: 1;">
                                    <small style="color: #FF7A00; display: block; margin-bottom: 12px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-shopping-cart" style="opacity: 0.7; margin-right: 6px;"></i> PCS Terpakai
                                    </small>
                                    <h2 style="color: #FF6B00; font-weight: 700; margin: 0; font-size: 40px; line-height: 1.1;">
                                        {{ $pcsTerpakai }}
                                    </h2>
                                </div>
                                <div style="text-align: right;">
                                    <i class="fas fa-shopping-cart" style="font-size: 56px; color: #F59E0B; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-0" style="background-color: #E8F5E9; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-flex-start justify-content-between">
                                <div style="flex: 1;">
                                    <small style="color: #00AA00; display: block; margin-bottom: 12px; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-check-circle" style="opacity: 0.7; margin-right: 6px;"></i> PCS Tersisa
                                    </small>
                                    <h2 style="color: #00AA00; font-weight: 700; margin: 0; font-size: 40px; line-height: 1.1;">
                                        {{ $pcsSisa }}
                                    </h2>
                                </div>
                                <div style="text-align: right;">
                                    <i class="fas fa-check-circle" style="font-size: 56px; color: #22C55E; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN FINANSIAL --}}
            <div class="card border-0 shadow-sm mb-4" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)';">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-bold" style="color: #F57C00;">
                        <i class="fas fa-calculator" style="opacity: 0.7; margin-right: 6px;"></i> Ringkasan Finansial
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block mb-2">Harga Beli per PCS</small>
                            <h5 class="fw-bold text-primary">
                                Rp {{ number_format(($stockGudang->harga_beli_pack ?? 0) / ($stockGudang->konversi_satuan ?? 1), 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px;">Total Modal Terpakai</small>
                            <h5 class="fw-bold mb-0" style="color: #F57C00;">
                                Rp {{ number_format((($stockGudang->harga_beli_pack ?? 0) / ($stockGudang->konversi_satuan ?? 1)) * $pcsTerpakai, 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small style="color: #65676B; display: block; margin-bottom: 8px; font-size: 12px;">Total Modal Sisa</small>
                            <h5 class="fw-bold mb-0" style="color: #2E7D32;">
                                Rp {{ number_format((($stockGudang->harga_beli_pack ?? 0) / ($stockGudang->konversi_satuan ?? 1)) * $pcsSisa, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMASI DARI PEMBELIAN --}}
            @if ($stockGudang->purchase_id)
                <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px; background-color: #E8F4FD; border-left: 4px solid #0084FF;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <i class="fas fa-info-circle" style="color: #0084FF; font-size: 20px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1" style="color: #0084FF; font-weight: 500;">Data dari Pembelian</p>
                                <p class="mb-0" style="color: #65676B; font-size: 13px;">Untuk mengubah data, silakan edit dari halaman pembelian.</p>
                            </div>
                            <a href="{{ route('pembelian.show', $stockGudang->pembelian) }}" class="btn btn-sm" style="background-color: #0084FF; color: white; border: none; border-radius: 6px;">
                                <i class="fas fa-external-link-alt"></i> Lihat Pembelian
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FORM PENGURANGAN STOK (ACCORDION) --}}
            <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px;">
                <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                    <button class="btn btn-link text-start w-100 p-0 text-decoration-none fw-bold" 
                            type="button" data-bs-toggle="collapse" data-bs-target="#reduceStockCollapse"
                            style="color: #1A1A1A; font-size: 14px;">
                        <i class="fas fa-chevron-down me-2" style="opacity: 0.6;"></i>
                        <i class="fas fa-minus-circle" style="color: #F57C00; opacity: 0.8;"></i> Pengurangan Stok Manual
                        <small style="color: #65676B; display: block; margin-top: 4px;">Untuk pengurangan non-penjualan</small>
                    </button>
                </div>
                <div id="reduceStockCollapse" class="collapse">
                    <div class="card-body p-4" style="border-top: 1px solid #E5E7EB;">
                        @if ($stockGudang->pcs_sisa <= 0)
                            <div class="alert mb-0" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFB74D; border-radius: 8px;">
                                <i class="fas fa-info-circle"></i> Stok habis. Tidak dapat mengurangi lebih lanjut.
                            </div>
                        @else
                            <form action="{{ route('stock-gudang.reduce-stock', $stockGudang) }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="jumlah_pengurangan" class="form-label fw-bold" style="color: #1A1A1A; font-size: 14px;">Jumlah Pengurangan (PCS)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('jumlah_pengurangan') is-invalid @enderror" 
                                                   id="jumlah_pengurangan" name="jumlah_pengurangan" 
                                                   value="{{ old('jumlah_pengurangan') }}"
                                                   min="1" max="{{ $stockGudang->pcs_sisa ?? 0 }}" 
                                                   placeholder="Contoh: 50" 
                                                   style="border-color: #D1D5DB; border-radius: 6px 0 0 6px;"
                                                   required>
                                            <span class="input-group-text" style="background-color: #F3F4F6; color: #65676B; border-color: #D1D5DB; font-size: 12px; border-radius: 0 6px 6px 0;">Stok: {{ $stockGudang->pcs_sisa ?? 0 }} PCS</span>
                                        </div>
                                        @error('jumlah_pengurangan')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="catatan_pengurangan" class="form-label fw-bold" style="color: #1A1A1A; font-size: 14px;">Catatan Pengurangan</label>
                                    <textarea class="form-control @error('catatan_pengurangan') is-invalid @enderror" 
                                              id="catatan_pengurangan" name="catatan_pengurangan" 
                                              rows="2" 
                                              placeholder="Contoh: Digunakan untuk tester, Rusak, Kalibrasi, dll" 
                                              style="border-color: #D1D5DB; border-radius: 6px;"
                                              required>{{ old('catatan_pengurangan') }}</textarea>
                                    <small style="color: #65676B; font-size: 12px;">Pengurangan ini tidak termasuk dalam penjualan</small>
                                    @error('catatan_pengurangan')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn fw-bold" style="background-color: #F57C00; color: white; border: none; border-radius: 6px;">
                                    <i class="fas fa-minus-circle"></i> Kurangi Stok
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIWAYAT PENGURANGAN STOK --}}
            @php
                $adjustments = $stockGudang->stockAdjustments;
            @endphp

            @if ($adjustments->count() > 0)
                <div class="card border-0 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 12px;">
                    <div class="card-header border-0 p-4" style="background-color: #FFFFFF;">
                        <h6 class="mb-0 fw-bold" style="color: #1A1A1A; font-size: 14px;">
                            <i class="fas fa-history" style="opacity: 0.6; margin-right: 8px; color: #0084FF;"></i> Riwayat Pengurangan Stok
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0" style="border-collapse: collapse;">
                            <thead style="background-color: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                                <tr>
                                    <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 12px 16px; text-align: left;">Tanggal</th>
                                    <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 12px 16px; text-align: left;">Jumlah</th>
                                    <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 12px 16px; text-align: left;">Catatan</th>
                                    <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 12px 16px; text-align: left;">User</th>
                                    <th style="color: #65676B; font-weight: 600; font-size: 12px; padding: 12px 16px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adjustments as $adjustment)
                                    <tr style="border-bottom: 1px solid #E5E7EB;">
                                        <td style="color: #65676B; font-size: 13px; padding: 12px 16px;">{{ $adjustment->created_at->format('d M Y H:i') }}</td>
                                        <td style="padding: 12px 16px;">
                                            <span class="badge" style="background-color: #FFF3E0; color: #E65100; font-size: 12px;">{{ $adjustment->jumlah_pengurangan }} PCS</span>
                                        </td>
                                        <td style="color: #1A1A1A; font-size: 13px; padding: 12px 16px;">{{ $adjustment->catatan }}</td>
                                        <td style="color: #65676B; font-size: 13px; padding: 12px 16px;">{{ $adjustment->user->name ?? '-' }}</td>
                                        <td style="text-align: center; padding: 12px 16px;">
                                            <form action="{{ route('stock-adjustment.delete', $adjustment->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" style="background-color: transparent; color: #DC3545; border: 1px solid #DC3545; border-radius: 6px; padding: 4px 8px;" title="Batalkan">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background-color: #F9FAFB; border-top: 2px solid #E5E7EB;">
                                <tr>
                                    <th colspan="5" style="color: #1A1A1A; font-weight: 600; padding: 12px 16px; text-align: right;">
                                        Total Pengurangan: <span style="color: #F57C00;">{{ $adjustments->sum('jumlah_pengurangan') }} PCS</span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="d-flex gap-2 mb-4">
                @if (!$stockGudang->purchase_id)
                    <a href="{{ route('stock-gudang.edit', $stockGudang) }}" class="btn fw-bold" style="background-color: #F57C00; color: white; border: none; border-radius: 6px;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                
                <form action="{{ route('stock-gudang.destroy', $stockGudang) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn fw-bold" style="background-color: #DC3545; color: white; border: none; border-radius: 6px;" onclick="return confirm('Yakin ingin menghapus?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>

                <a href="{{ route('stock-gudang.index') }}" class="btn fw-bold ms-auto" style="background-color: #E5E7EB; color: #1A1A1A; border: none; border-radius: 6px;">
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
    
    .form-control {
        border-color: #D1D5DB;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .form-control:focus {
        border-color: #0084FF;
        box-shadow: 0 0 0 3px rgba(0, 132, 255, 0.1);
    }
</style>

<script>
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const jumlah = this.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
        const catatan = this.closest('tr').querySelector('td:nth-child(3)').textContent.trim();
        
        if (confirm(`Batalkan pengurangan?\n\nJumlah: ${jumlah}\nCatatan: ${catatan}`)) {
            this.submit();
        }
    });
});
</script>
@endsection
