@extends('layouts.app')

@section('title', 'Tambah Produk Siap Jual')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Produk Siap Jual</h5>
                </div>
                <div class="card-body">
                    @if(($defaultTipeProduk ?? 'single') === 'paket' && ($prefilledPaketId ?? null))
                        <div class="alert alert-info">
                            <i class="fas fa-link"></i>
                            Alur terintegrasi aktif: Paket dipilih lebih dulu, lalu dibuat sebagai Produk Siap Jual sebelum dipublikasikan ke guest.
                        </div>
                    @endif

                    <form action="{{ route('produk-siap-jual.store') }}" method="POST" id="form-produk" enctype="multipart/form-data">
                        @csrf

                        <!-- SUMBER PRODUK SECTION (NEW) -->
                        <h6 class="mb-3"><strong><i class="fas fa-cubes"></i> Sumber Produk</strong></h6>
                        <hr>

                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_produk" id="tipe_single"
                                       value="single" {{ old('tipe_produk', $defaultTipeProduk ?? 'single') === 'single' ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipe_single">
                                    <i class="fas fa-box text-primary"></i> Produk Tunggal (Stock Gudang)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_produk" id="tipe_paket"
                                       value="paket" {{ old('tipe_produk', $defaultTipeProduk ?? 'single') === 'paket' ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipe_paket">
                                    <i class="fas fa-layer-group text-success"></i> Produk Paket / Platter
                                </label>
                            </div>
                        </div>

                        <!-- STOCK GUDANG DROPDOWN (untuk single) -->
                        <div class="mb-3" id="section-stock-gudang">
                            <label for="stock_gudang_id" class="form-label">Stock Gudang <span class="text-danger">*</span></label>
                            <select class="form-select @error('stock_gudang_id') is-invalid @enderror"
                                    id="stock_gudang_id" name="stock_gudang_id">
                                <option value="">Pilih Stock Produk</option>
                                @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}"
                                            data-hpp="{{ ($stock->harga_beli_pack ?? 0) / max(1, $stock->konversi_satuan ?? 1) }}"
                                            data-nama="{{ $stock->nama_produk }}"
                                            {{ old('stock_gudang_id') == $stock->id ? 'selected' : '' }}>
                                        [{{ $stock->sku }}] {{ $stock->nama_produk }} (Stok: {{ $stock->pcs_sisa ?? 0 }} pcs)
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_gudang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PRODUK PAKET DROPDOWN (untuk paket) -->
                        <div class="mb-3" id="section-produk-paket" style="display: none;">
                            <label for="produk_paket_id" class="form-label">Produk Paket <span class="text-danger">*</span></label>
                            <select class="form-select @error('produk_paket_id') is-invalid @enderror"
                                    id="produk_paket_id" name="produk_paket_id">
                                <option value="">Pilih Produk Paket</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->id }}"
                                            data-hpp="{{ $paket->hpp_total ?? 0 }}"
                                            data-nama="{{ $paket->nama_paket }}"
                                            data-details="{{ $paket->details_count ?? 0 }}"
                                            {{ old('produk_paket_id', $prefilledPaketId ?? '') == $paket->id ? 'selected' : '' }}>
                                        {{ $paket->nama_paket }} ({{ $paket->details_count ?? 0 }} item, HPP: Rp {{ number_format($paket->hpp_total ?? 0, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('produk_paket_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> HPP akan dihitung otomatis dari komposisi paket
                            </small>
                        </div>

                        <!-- BASIC INFO SECTION -->
                        <h6 class="mb-3 mt-4"><strong><i class="fas fa-box"></i> Informasi Dasar</strong></h6>
                        <hr>

                        <!-- Nama Produk (optional override) -->
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                                   id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $prefilledPaketName ?? '') }}"
                                   placeholder="Kosongkan untuk menggunakan nama dari sumber">
                            @error('nama_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gambar_produk" class="form-label">Foto Produk</label>
                            <input type="file" class="form-control @error('gambar_produk') is-invalid @enderror"
                                   id="gambar_produk" name="gambar_produk" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Foto akan tampil di guest.</small>
                            @error('gambar_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hpp_per_pcs" class="form-label">HPP per PCS <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('hpp_per_pcs') is-invalid @enderror"
                                       id="hpp_per_pcs" name="hpp_per_pcs" value="{{ old('hpp_per_pcs') }}"
                                       min="0" step="0.01" placeholder="Rp" required>
                                <small class="text-muted" id="hpp-info">Akan diisi otomatis dari sumber produk</small>
                                @error('hpp_per_pcs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pcs_per_paket" class="form-label">Isi PCS per Paket <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('pcs_per_paket') is-invalid @enderror"
                                       id="pcs_per_paket" name="pcs_per_paket" value="{{ old('pcs_per_paket', 1) }}"
                                       min="1" step="1" placeholder="Contoh: 6" required>
                                <small class="text-muted">Berapa PCS dalam 1 paket?</small>
                                @error('pcs_per_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="margin_laba" class="form-label">Margin Laba (%)</label>
                                <input type="number" class="form-control @error('margin_laba') is-invalid @enderror"
                                       id="margin_laba" name="margin_laba" value="{{ old('margin_laba', 0) }}"
                                       min="0" step="0.01" placeholder="Contoh: 25">
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
                                       id="biaya_packing" name="biaya_packing" value="{{ old('biaya_packing', 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_packing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="biaya_saos" class="form-label">Biaya Saos (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_saos') is-invalid @enderror"
                                       id="biaya_saos" name="biaya_saos" value="{{ old('biaya_saos', 0) }}"
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
                                       id="biaya_sumpit" name="biaya_sumpit" value="{{ old('biaya_sumpit', 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_sumpit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="biaya_tenaga" class="form-label">Biaya Tenaga (Rp)</label>
                                <input type="number" class="form-control biaya-input @error('biaya_tenaga') is-invalid @enderror"
                                       id="biaya_tenaga" name="biaya_tenaga" value="{{ old('biaya_tenaga', 0) }}"
                                       min="0" step="0.01" placeholder="Rp 0">
                                @error('biaya_tenaga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- CALCULATION RESULT SECTION -->
                        <h6 class="mb-3 mt-4"><strong><i class="fas fa-calculator"></i> Ringkasan Perhitungan Harga</strong></h6>
                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HPP per PCS (Hanya Bahan Baku)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="hpp_per_pcs_display"
                                           value="0" readonly style="background-color: #e8f5e9;">
                                </div>
                                <small class="text-muted">= Harga Beli Pack ÷ Konversi Satuan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Biaya Lain-lain (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="total_biaya_lain"
                                           value="0" readonly style="background-color: #fff3cd;">
                                </div>
                                <small class="text-muted">Packing + Saos + Sumpit + Tenaga (Per Paket)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HPP Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="hpp_paket"
                                           value="0" readonly style="background-color: #fff3cd;">
                                </div>
                                <small class="text-muted">= HPP per PCS × PCS per Paket</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modal Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-warning" id="modal_paket"
                                           value="0" readonly style="background-color: #fffbf0;">
                                </div>
                                <small class="text-muted">= HPP Paket + Total Biaya Lain</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Jual per PCS (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-success" id="harga_jual_per_pcs"
                                           value="0" readonly style="background-color: #e8f5e9;">
                                </div>
                                <small class="text-muted">= Harga Jual Paket ÷ PCS per Paket</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Jual per Paket (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold text-success" id="harga_jual_per_paket"
                                           value="0" readonly style="background-color: #e8f5e9;">
                                </div>
                                <small class="text-muted">= Modal Paket × (1 + Margin %)</small>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
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
    const tipeSingle = document.getElementById('tipe_single');
    const tipePaket = document.getElementById('tipe_paket');
    const sectionStockGudang = document.getElementById('section-stock-gudang');
    const sectionProdukPaket = document.getElementById('section-produk-paket');
    const stockGudangSelect = document.getElementById('stock_gudang_id');
    const produkPaketSelect = document.getElementById('produk_paket_id');
    const hppPerPcs = document.getElementById('hpp_per_pcs');
    const hppInfo = document.getElementById('hpp-info');
    const pcsPerPaket = document.getElementById('pcs_per_paket');
    const marginLaba = document.getElementById('margin_laba');
    const biayaInputs = document.querySelectorAll('.biaya-input');
    const hppPerPcsDisplay = document.getElementById('hpp_per_pcs_display');
    const totalBiayaLain = document.getElementById('total_biaya_lain');
    const hppPaket = document.getElementById('hpp_paket');
    const modalPaket = document.getElementById('modal_paket');
    const hargaJualPerPcs = document.getElementById('harga_jual_per_pcs');
    const hargaJualPerPaket = document.getElementById('harga_jual_per_paket');

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // === TOGGLE SUMBER PRODUK ===
    function toggleSumberProduk() {
        if (tipePaket.checked) {
            sectionStockGudang.style.display = 'none';
            sectionProdukPaket.style.display = 'block';
            stockGudangSelect.removeAttribute('required');
            produkPaketSelect.setAttribute('required', 'required');
            hppInfo.textContent = 'Dihitung dari komposisi paket';

            // Update HPP dari paket
            updateHppFromPaket();
        } else {
            sectionStockGudang.style.display = 'block';
            sectionProdukPaket.style.display = 'none';
            stockGudangSelect.setAttribute('required', 'required');
            produkPaketSelect.removeAttribute('required');
            hppInfo.textContent = 'Dari harga beli stock gudang';

            // Update HPP dari stock gudang
            updateHppFromStock();
        }
    }

    // === UPDATE HPP FROM STOCK GUDANG ===
    function updateHppFromStock() {
        const selected = stockGudangSelect.options[stockGudangSelect.selectedIndex];
        if (selected && selected.value) {
            const hpp = parseFloat(selected.dataset.hpp) || 0;
            hppPerPcs.value = hpp.toFixed(2);
        }
        calculate();
    }

    // === UPDATE HPP FROM PRODUK PAKET ===
    function updateHppFromPaket() {
        const selected = produkPaketSelect.options[produkPaketSelect.selectedIndex];
        if (selected && selected.value) {
            const hppTotal = parseFloat(selected.dataset.hpp) || 0;
            const pcs = parseInt(pcsPerPaket.value) || 1;
            // Untuk paket, HPP per PCS = HPP total paket / pcs_per_paket
            const hppPerPcsValue = hppTotal / Math.max(1, pcs);
            hppPerPcs.value = hppPerPcsValue.toFixed(2);
        }
        calculate();
    }

    function calculate() {
        // 1️⃣ Get values
        const hpp = parseFloat(hppPerPcs.value) || 0;
        const pcsPerPaketVal = parseInt(pcsPerPaket.value) || 1;
        const margin = parseFloat(marginLaba.value) || 0;

        // 2️⃣ Calculate total biaya lain-lain per PAKET
        let totalBiaya = 0;
        biayaInputs.forEach(input => {
            totalBiaya += parseFloat(input.value) || 0;
        });

        // 3️⃣ HPP Paket = HPP per PCS × PCS per Paket
        const hppPaketValue = hpp * pcsPerPaketVal;

        // 4️⃣ Modal Paket = HPP Paket + Total Biaya Lain
        const modalPaketValue = hppPaketValue + totalBiaya;

        // 5️⃣ Harga Jual Paket = Modal Paket × (1 + Margin / 100)
        const hargaJualPerPaketValue = modalPaketValue * (1 + (margin / 100));

        // 6️⃣ Harga Jual per PCS = Harga Jual Paket / PCS per Paket
        const hargaJualPerPcsValue = hargaJualPerPaketValue / pcsPerPaketVal;

        // 7️⃣ Update displays
        hppPerPcsDisplay.value = formatCurrency(hpp);
        totalBiayaLain.value = formatCurrency(totalBiaya);
        hppPaket.value = formatCurrency(hppPaketValue);
        modalPaket.value = formatCurrency(modalPaketValue);
        hargaJualPerPcs.value = formatCurrency(hargaJualPerPcsValue);
        hargaJualPerPaket.value = formatCurrency(hargaJualPerPaketValue);
    }

    // Event listeners untuk toggle tipe produk
    tipeSingle.addEventListener('change', toggleSumberProduk);
    tipePaket.addEventListener('change', toggleSumberProduk);

    // Event listeners untuk dropdown
    stockGudangSelect.addEventListener('change', updateHppFromStock);
    produkPaketSelect.addEventListener('change', updateHppFromPaket);

    // Event listeners untuk kalkulasi
    hppPerPcs.addEventListener('change', calculate);
    hppPerPcs.addEventListener('input', calculate);
    pcsPerPaket.addEventListener('change', function() {
        // Update HPP jika tipe paket (karena HPP per PCS = HPP total / pcs_per_paket)
        if (tipePaket.checked) {
            updateHppFromPaket();
        }
        calculate();
    });
    pcsPerPaket.addEventListener('input', function() {
        if (tipePaket.checked) {
            updateHppFromPaket();
        }
        calculate();
    });
    marginLaba.addEventListener('change', calculate);
    marginLaba.addEventListener('input', calculate);
    biayaInputs.forEach(input => {
        input.addEventListener('change', calculate);
        input.addEventListener('input', calculate);
    });

    // Initial setup
    toggleSumberProduk();
    calculate();
});
</script>
@endsection

