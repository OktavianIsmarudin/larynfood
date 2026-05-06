@extends('layouts.app')

@section('title', 'Edit Produk Siap Jual')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Produk Siap Jual</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('produk-siap-jual.update', $produkSiapJual) }}" method="POST" id="form-produk" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- BASIC INFO SECTION -->
                        <h6 class="mb-3 mt-4"><strong><i class="fas fa-box"></i> Informasi Dasar</strong></h6>
                        <hr>

                        <div class="mb-3">
                            <label for="stock_gudang_id" class="form-label">Stock Gudang <span class="text-danger">*</span></label>
                            <select class="form-select @error('stock_gudang_id') is-invalid @enderror"
                                    id="stock_gudang_id" name="stock_gudang_id" required>
                                <option value="">Pilih Stock Produk</option>
                                @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}" {{ old('stock_gudang_id', $produkSiapJual->stock_gudang_id) == $stock->id ? 'selected' : '' }}>
                                        [{{ $stock->sku }}] {{ $stock->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_gudang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Produk</label>
                            @if($produkSiapJual->gambar_produk)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $produkSiapJual->gambar_produk) }}"
                                         alt="{{ $produkSiapJual->nama_produk }}"
                                         style="max-width: 220px; max-height: 160px; object-fit: cover; border-radius: 10px; border: 1px solid #e5e7eb;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('gambar_produk') is-invalid @enderror"
                                   id="gambar_produk" name="gambar_produk" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                            @error('gambar_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hpp_per_pcs" class="form-label">HPP per PCS <span class="text-danger">*</span> <span class="badge bg-info">TERKUNCI</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control"
                                           id="hpp_per_pcs" readonly
                                           value="{{ number_format($produkSiapJual->hpp_per_pcs, 2, ',', '.') }}">
                                </div>
                                <!-- Hidden field untuk mengirimkan nilai yang sebenarnya -->
                                <input type="hidden" name="hpp_per_pcs" value="{{ $produkSiapJual->hpp_per_pcs }}">
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-lock"></i> HPP sudah diset sebelumnya dan tidak bisa diubah pada saat edit.
                                    <br>Jika perlu mengubah HPP, silahkan hapus dan buat produk baru.
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pcs_per_paket" class="form-label">Jumlah PCS per Paket <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('pcs_per_paket') is-invalid @enderror"
                                       id="pcs_per_paket" name="pcs_per_paket" value="{{ old('pcs_per_paket', $produkSiapJual->pcs_per_paket ?? 1) }}"
                                       min="1" step="1" required>
                                @error('pcs_per_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="margin_laba" class="form-label">Margin Laba (%)</label>
                                <input type="number" class="form-control @error('margin_laba') is-invalid @enderror"
                                       id="margin_laba" name="margin_laba" value="{{ old('margin_laba', $produkSiapJual->margin_laba) }}"
                                       min="0" step="0.01">
                                @error('margin_laba')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- BIAYA LAIN-LAIN SECTION -->
                        <h6 class="mb-3 mt-4"><strong><i class="fas fa-receipt"></i> Biaya Lain-lain (Per Paket)</strong></h6>
                        <hr>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle"></i>
                            <strong>Biaya lain-lain dihitung PER PAKET, bukan per PCS</strong>
                            <br>Contoh: Jika biaya packing Rp 1000, itu untuk 1 paket utuh, bukan untuk 1 PCS
                        </p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="biaya_packing" class="form-label">Biaya Packing (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_packing') is-invalid @enderror"
                                       id="biaya_packing" name="biaya_packing" value="{{ old('biaya_packing', $produkSiapJual->biaya_packing ?? 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_packing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="biaya_saos" class="form-label">Biaya Saos (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_saos') is-invalid @enderror"
                                       id="biaya_saos" name="biaya_saos" value="{{ old('biaya_saos', $produkSiapJual->biaya_saos ?? 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_saos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="biaya_sumpit" class="form-label">Biaya Sumpit (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_sumpit') is-invalid @enderror"
                                       id="biaya_sumpit" name="biaya_sumpit" value="{{ old('biaya_sumpit', $produkSiapJual->biaya_sumpit ?? 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_sumpit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="biaya_tenaga" class="form-label">Biaya Tenaga (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_tenaga') is-invalid @enderror"
                                       id="biaya_tenaga" name="biaya_tenaga" value="{{ old('biaya_tenaga', $produkSiapJual->biaya_tenaga ?? 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_tenaga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- CALCULATION RESULT SECTION -->
                        <h6 class="mb-3 mt-4"><strong><i class="fas fa-calculator"></i> Ringkasan Perhitungan Paket</strong></h6>
                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HPP Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="hpp_paket"
                                           value="{{ number_format($produkSiapJual->hpp_paket ?? 0, 2, ',', '.') }}" readonly style="background-color: #f0f0f0;">
                                </div>
                                <small class="text-muted">= HPP per PCS × Jumlah PCS</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Biaya Lain-lain (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="total_biaya_lain"
                                           value="{{ number_format($produkSiapJual->total_biaya_lain ?? 0, 2, ',', '.') }}" readonly style="background-color: #f0f0f0;">
                                </div>
                                <small class="text-muted">Packing + Saos + Sumpit + Tenaga</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modal Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="modal_paket"
                                           value="{{ number_format($produkSiapJual->modal_paket ?? 0, 2, ',', '.') }}" readonly style="background-color: #f0f0f0;">
                                </div>
                                <small class="text-muted">= HPP Paket + Total Biaya Lain</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Jual Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-success" id="harga_jual_per_pcs"
                                           value="{{ number_format($produkSiapJual->harga_jual_per_pcs ?? 0, 2, ',', '.') }}" readonly style="background-color: #f0f0f0;">
                                </div>
                                <small class="text-muted">= Modal Paket + (Modal Paket × Margin %)</small>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <small>
                                <strong>📊 Formula Perhitungan Harga (LOGIKA BARU):</strong><br>
                                1. <strong>Total Biaya Lain-lain</strong> = Packing + Saos + Sumpit + Tenaga (per paket)<br>
                                2. <strong>HPP Paket</strong> = HPP per PCS × PCS per Paket<br>
                                3. <strong>Modal Paket</strong> = HPP Paket + Total Biaya Lain-lain<br>
                                4. <strong>Harga Jual Paket</strong> = Modal Paket × (1 + Margin Laba ÷ 100)<br>
                                5. <strong>Harga Jual per PCS</strong> = Harga Jual Paket ÷ PCS per Paket<br>
                                <br>
                                <strong>⚠️ Catatan Penting:</strong><br>
                                • HPP per PCS <strong>HANYA</strong> harga bahan baku, TIDAK termasuk biaya lain-lain<br>
                                • Biaya lain-lain dihitung <strong>PER PAKET</strong>, bukan per PCS<br>
                                • Margin diterapkan pada Modal Paket, bukan pada HPP per PCS
                            </small>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                            <a href="{{ route('produk-siap-jual.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-produk');
    const hppPerPcs = document.getElementById('hpp_per_pcs');
    const jumlahPcsJual = document.getElementById('jumlah_pcs_jual');
    const marginLaba = document.getElementById('margin_laba');
    const biayaInputs = document.querySelectorAll('.biaya-input');
    const hppPaket = document.getElementById('hpp_paket');
    const totalBiayaLain = document.getElementById('total_biaya_lain');
    const modalPaket = document.getElementById('modal_paket');
    const hargaJualPerPcs = document.getElementById('harga_jual_per_pcs');

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    function calculate() {
        // Get values
        const hpp = parseFloat(hppPerPcs.value) || 0;
        const jumlah = parseInt(jumlahPcsJual.value) || 1;
        const margin = parseFloat(marginLaba.value) || 0;

        // Calculate total biaya lain
        let totalBiaya = 0;
        biayaInputs.forEach(input => {
            totalBiaya += parseFloat(input.value) || 0;
        });

        // Calculate HPP Paket (HPP per PCS × Jumlah PCS)
        const hppPaketValue = hpp * jumlah;

        // Calculate Modal Paket (HPP Paket + Total Biaya Lain)
        const modalPaketValue = hppPaketValue + totalBiaya;

        // Calculate Harga Jual (Modal Paket + (Modal Paket × Margin / 100))
        const hargaJual = modalPaketValue + (modalPaketValue * margin / 100);

        // Update displays
        hppPaket.value = formatCurrency(hppPaketValue);
        totalBiayaLain.value = formatCurrency(totalBiaya);
        modalPaket.value = formatCurrency(modalPaketValue);
        hargaJualPerPcs.value = formatCurrency(hargaJual);
    }

    // Event listeners
    hppPerPcs.addEventListener('change', calculate);
    hppPerPcs.addEventListener('input', calculate);
    jumlahPcsJual.addEventListener('change', calculate);
    jumlahPcsJual.addEventListener('input', calculate);
    marginLaba.addEventListener('change', calculate);
    marginLaba.addEventListener('input', calculate);
    biayaInputs.forEach(input => {
        input.addEventListener('change', calculate);
        input.addEventListener('input', calculate);
    });

    // Initial calculation
    calculate();
});
</script>
@endsection
