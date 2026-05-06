@extends('layouts.app')

@section('title', 'Tambah Penjualan')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Penjualan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('penjualan.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_customer" class="form-label">Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_customer') is-invalid @enderror" 
                                       id="nama_customer" name="nama_customer" value="{{ old('nama_customer') }}" 
                                       list="customer_list" placeholder="Ketik nama customer atau pilih dari daftar" required>
                                <datalist id="customer_list">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->nama_customer }}" label="{{ $customer->telepon ?? '' }}">
                                    @endforeach
                                </datalist>
                                <small class="text-muted d-block mt-1">💡 Customer baru akan otomatis ditambahkan jika belum ada</small>
                                @error('nama_customer')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tanggal_penjualan" class="form-label">Tanggal Penjualan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_penjualan') is-invalid @enderror" 
                                       id="tanggal_penjualan" name="tanggal_penjualan" value="{{ old('tanggal_penjualan', now()->format('Y-m-d')) }}" required>
                                @error('tanggal_penjualan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="produk_siap_jual_id" class="form-label">Produk <span class="text-danger">*</span></label>
                                <select class="form-select @error('produk_siap_jual_id') is-invalid @enderror" id="produk_siap_jual_id" name="produk_siap_jual_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produk as $p)
                                        @php
                                            // Stok dalam satuan PAKET (sesuai halaman Produk Siap Jual)
                                            $stokPaket = (int)($p->stok_siap_jual ?? 0);
                                            $pcsPerPaket = (int)($p->pcs_per_paket ?? 1);
                                            $stokPcs = $stokPaket * $pcsPerPaket;
                                            $hargaSatuan = (float)($p->harga_jual ?? 0);
                                            $hppPerPcs = (float)($p->hpp_per_pcs ?? 0);
                                            $marginLaba = (float)($p->margin_laba ?? 0);
                                            $isOutOfStock = $stokPaket <= 0;
                                        @endphp
                                        <option value="{{ $p->id }}" 
                                                data-harga="{{ $hargaSatuan }}"
                                                data-stok="{{ $stokPaket }}"
                                                data-pcs-per-paket="{{ $pcsPerPaket }}"
                                                data-hpp-per-pcs="{{ $hppPerPcs }}"
                                                data-margin-laba="{{ $marginLaba }}"
                                                data-nama-produk="{{ $p->nama_produk }}"
                                                {{ $isOutOfStock ? 'disabled' : '' }}
                                                {{ old('produk_siap_jual_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_produk }} (Stok: {{ $stokPaket }} paket) {{ $isOutOfStock ? '❌ Habis' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('produk_siap_jual_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    💡 Stok diambil dari Produk Siap Jual (dalam satuan paket)
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="metode_pembayaran_id" class="form-label">Metode Pembayaran</label>
                                <select class="form-select @error('metode_pembayaran_id') is-invalid @enderror" id="metode_pembayaran_id" name="metode_pembayaran_id">
                                    <option value="">Pilih Metode</option>
                                    @foreach ($metode as $m)
                                        <option value="{{ $m->id }}" {{ old('metode_pembayaran_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama_metode }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('metode_pembayaran_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- INFORMASI HPP DARI PRODUK SIAP JUAL --}}
                        <div class="row mb-4" id="hpp-info-section" style="display: none;">
                            <div class="col-md-12">
                                <div class="card border-info bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-tag text-info"></i> Informasi HPP & Harga
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">HPP per PCS</small>
                                                <p class="fw-bold text-danger mb-0">
                                                    <span id="hpp-per-pcs-display">Rp 0</span>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Harga Jual per PCS</small>
                                                <p class="fw-bold text-success mb-0">
                                                    <span id="harga-jual-display">Rp 0</span>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Margin Laba</small>
                                                <p class="fw-bold text-primary mb-0">
                                                    <span id="margin-laba-display">0%</span>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Stok Tersedia</small>
                                                <p class="fw-bold text-warning mb-0">
                                                    <span id="stok-tersedia-display">0 PCS</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_pcs" class="form-label">Jumlah Paket <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jumlah_pcs') is-invalid @enderror" 
                                       id="jumlah_pcs" name="jumlah_pcs" value="{{ old('jumlah_pcs') }}" min="1" required>
                                @error('jumlah_pcs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1" id="info-pcs-total" style="display:none;">
                                    💡 = <span id="total-pcs-info">0</span> PCS
                                </small>
                                <div id="stok-warning" class="alert alert-danger mt-2 py-2 px-3" style="display: none; font-size: 13px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span id="stok-warning-text"></span>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" 
                                       id="harga_satuan_display" placeholder="Rp 0" readonly>
                                <input type="hidden" id="harga_satuan" name="harga_satuan" value="0" required>
                                <small class="text-muted d-block mt-1">Readonly - diisi otomatis dari produk</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="total_penjualan" class="form-label">Total Penjualan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" 
                                       id="total_penjualan" readonly placeholder="Rp 0" required>
                                <small class="text-muted d-block mt-1">Readonly - dihitung otomatis</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="diskon" class="form-label">Diskon</label>
                                <input type="number" class="form-control @error('diskon') is-invalid @enderror" 
                                       id="diskon" name="diskon" value="{{ old('diskon', 0) }}" min="0" step="0.01" placeholder="0">
                                @error('diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipe_diskon" class="form-label">Tipe Diskon</label>
                                <select class="form-select @error('tipe_diskon') is-invalid @enderror" id="tipe_diskon" name="tipe_diskon">
                                    <option value="">Pilih Tipe</option>
                                    <option value="nominal" {{ old('tipe_diskon') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    <option value="persentase" {{ old('tipe_diskon') == 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                                </select>
                                @error('tipe_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="total_bayar" class="form-label">Total Bayar (Setelah Diskon) <span class="text-success fw-bold">*</span></label>
                                <input type="text" class="form-control" 
                                       id="total_bayar" readonly placeholder="Rp 0" required>
                                <small class="text-muted d-block mt-1">Readonly - dihitung otomatis dengan diskon</small>
                            </div>
                            <div class="col-md-6">
                                <label for="potongan_diskon" class="form-label">Potongan Diskon</label>
                                <input type="text" class="form-control" 
                                       id="potongan_diskon" readonly placeholder="Rp 0">
                                <small class="text-muted d-block mt-1">Display info</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ongkir" class="form-label">Ongkir (Biaya Pengiriman)</label>
                                <input type="number" class="form-control @error('ongkir') is-invalid @enderror" 
                                       id="ongkir" name="ongkir" value="{{ old('ongkir', 0) }}" min="0" step="0.01" placeholder="0">
                                @error('ongkir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">💡 Biaya pengiriman/ongkos antar (jika ada)</small>
                            </div>

                            <div class="col-md-6">
                                <label for="total_akhir" class="form-label">Total Akhir (+ Ongkir) <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control" 
                                       id="total_akhir" readonly placeholder="Rp 0" style="background-color: #fff3cd; font-weight: bold; font-size: 1.1rem;" required>
                                <small class="text-muted d-block mt-1">Total pembayaran terakhir termasuk ongkir</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Informasi Pengiriman)</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Kirim ke rumah, jam 2-3 sore, mohon ditelepon ketika tiba..." maxlength="1000">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">💡 Catatan khusus untuk pengiriman (alamat detail, jam pengiriman, instruksi khusus, dll)</small>
                        </div>

                        {{-- OPSIONAL: Penggunaan Modal --}}
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body py-3">
                                <h6 class="text-muted mb-3"><i class="fas fa-wallet"></i> Penggunaan Modal (Opsional)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="modal_terpakai" class="form-label">Modal Terpakai</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('modal_terpakai') is-invalid @enderror" 
                                                   id="modal_terpakai" name="modal_terpakai" value="{{ old('modal_terpakai') }}" 
                                                   min="0" step="0.01" placeholder="0">
                                        </div>
                                        @error('modal_terpakai')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Jumlah modal yang dipakai untuk transaksi ini</small>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="keterangan_modal" class="form-label">Keterangan Modal</label>
                                        <input type="text" class="form-control @error('keterangan_modal') is-invalid @enderror" 
                                               id="keterangan_modal" name="keterangan_modal" value="{{ old('keterangan_modal') }}" 
                                               placeholder="Contoh: Modal dari kas harian" maxlength="255">
                                        @error('keterangan_modal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Field ini opsional dan TIDAK mempengaruhi perhitungan harga/laba/total.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_pembayaran" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('status_pembayaran') is-invalid @enderror" id="status_pembayaran" name="status_pembayaran" required>
                                <option value="lunas" {{ old('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="dp" {{ old('status_pembayaran') == 'dp' ? 'selected' : '' }}>DP (Down Payment)</option>
                                <option value="utang" {{ old('status_pembayaran') == 'utang' ? 'selected' : '' }}>Utang</option>
                            </select>
                            @error('status_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-simpan">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
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
    // Format currency display
    function formatCurrency(value) {
        if (!value || isNaN(value)) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(value);
    }

    // Parse currency string ke number
    function parseCurrency(text) {
        if (!text) return 0;
        // Remove "Rp" dan semua non-digit characters
        const cleaned = text.replace(/[^\d]/g, '');
        return parseFloat(cleaned) || 0;
    }

    function hitungTotal() {
        // Ambil nilai harga dari input field (numerik) - dari hidden field
        const hargaSatuan = parseFloat(document.getElementById('harga_satuan').value) || 0;
        
        const jumlahPaket = parseFloat(document.getElementById('jumlah_pcs').value) || 0;
        const diskon = parseFloat(document.getElementById('diskon').value) || 0;
        const tipeDiskon = document.getElementById('tipe_diskon').value || 'nominal';
        const ongkir = parseFloat(document.getElementById('ongkir').value) || 0;

        // Ambil pcs_per_paket dari produk yang dipilih
        const produkSelect = document.getElementById('produk_siap_jual_id');
        const selectedOption = produkSelect.options[produkSelect.selectedIndex];
        const pcsPerPaket = parseInt(selectedOption?.dataset?.pcsPerPaket) || 1;

        // Hitung total PCS = jumlah_paket × pcs_per_paket
        const totalPcs = jumlahPaket * pcsPerPaket;

        // Update info PCS
        const infoPcs = document.getElementById('info-pcs-total');
        const totalPcsInfo = document.getElementById('total-pcs-info');
        if (jumlahPaket > 0 && produkSelect.value) {
            infoPcs.style.display = 'block';
            totalPcsInfo.textContent = totalPcs;
        } else {
            infoPcs.style.display = 'none';
        }

        // Hitung total penjualan = jumlahPaket × harga_satuan (harga adalah per paket)
        const totalPenjualan = jumlahPaket * hargaSatuan;

        // Hitung potongan diskon
        let potonganDiskon = 0;
        if (diskon > 0) {
            if (tipeDiskon === 'persentase') {
                potonganDiskon = (totalPenjualan * diskon) / 100;
            } else {
                potonganDiskon = diskon;
            }
        }

        // Hitung total bayar = total penjualan - potongan diskon
        const totalBayar = Math.max(0, totalPenjualan - potonganDiskon);
        
        // Hitung total akhir = total bayar + ongkir
        const totalAkhir = totalBayar + ongkir;

        // Tampilkan total penjualan, potongan diskon, total bayar, dan total akhir
        document.getElementById('total_penjualan').value = formatCurrency(totalPenjualan);
        document.getElementById('potongan_diskon').value = formatCurrency(potonganDiskon);
        document.getElementById('total_bayar').value = formatCurrency(totalBayar);
        document.getElementById('total_akhir').value = formatCurrency(totalAkhir);
    }

    // Tunggu DOM siap sebelum attach events
    document.addEventListener('DOMContentLoaded', function() {
        const produkSelect = document.getElementById('produk_siap_jual_id');
        const jumlahPcsInput = document.getElementById('jumlah_pcs');
        const diskonInput = document.getElementById('diskon');
        const tipeDiskonSelect = document.getElementById('tipe_diskon');
        const ongkirInput = document.getElementById('ongkir');
        const hargaSatuanField = document.getElementById('harga_satuan');
        const form = document.querySelector('form');

        // Validasi form sebelum submit
        form.addEventListener('submit', function(e) {
            const selectedOption = produkSelect.options[produkSelect.selectedIndex];
            const stokPaket = parseInt(selectedOption?.dataset?.stok) || 0;
            const jumlah = parseInt(jumlahPcsInput.value) || 0;
            
            if (!produkSelect.value) {
                e.preventDefault();
                alert('❌ Silakan pilih produk terlebih dahulu.');
                return false;
            }

            if (stokPaket <= 0) {
                e.preventDefault();
                alert('❌ Stok produk habis atau tidak tersedia. Silakan pilih produk lain.');
                return false;
            }
            
            if (jumlah <= 0) {
                e.preventDefault();
                alert('❌ Jumlah paket harus lebih dari 0.');
                return false;
            }
            
            if (jumlah > stokPaket) {
                e.preventDefault();
                alert(`❌ Stok tidak mencukupi! Anda memasukkan ${jumlah} paket, tetapi stok tersedia hanya ${stokPaket} paket.\n\nSilakan kurangi jumlah pesanan.`);
                return false;
            }
        });

        // Event: Produk berubah
        produkSelect.addEventListener('change', function() {
            if (!this.value) {
                hargaSatuanField.value = '';
                document.getElementById('total_penjualan').value = '';
                document.getElementById('total_bayar').value = '';
                document.getElementById('total_akhir').value = '';
                document.getElementById('potongan_diskon').value = '';
                jumlahPcsInput.removeAttribute('max');
                jumlahPcsInput.value = '';
                document.getElementById('hpp-info-section').style.display = 'none';
                return;
            }
            
            const selectedOption = this.options[this.selectedIndex];
            
            // Check if product is disabled (out of stock)
            if (selectedOption.disabled) {
                alert('❌ Produk ini tidak memiliki stok tersedia');
                this.value = '';
                return;
            }
            
            const hargaSatuan = parseFloat(selectedOption.dataset.harga) || 0;
            const stock = parseInt(selectedOption.dataset.stok) || 0;
            const hppPerPcs = parseFloat(selectedOption.dataset.hppPerPcs) || 0;
            const marginLaba = parseFloat(selectedOption.dataset.marginLaba) || 0;

            // Cek jika stok <= 0
            if (stock <= 0) {
                alert('⚠️ Produk ini tidak memiliki stok. Silakan pilih produk lain.');
                this.value = '';
                return;
            }

            // Set harga satuan ke hidden field dengan nilai numerik, dan tampilkan di display field
            document.getElementById('harga_satuan').value = hargaSatuan.toFixed(2);
            document.getElementById('harga_satuan_display').value = formatCurrency(hargaSatuan);

            // Set max stock untuk validasi (langsung paket, bukan PCS)
            jumlahPcsInput.max = stock;
            
            // TAMPILKAN HPP INFORMATION
            document.getElementById('hpp-info-section').style.display = 'block';
            document.getElementById('hpp-per-pcs-display').textContent = formatCurrency(hppPerPcs);
            document.getElementById('harga-jual-display').textContent = formatCurrency(hargaSatuan);
            document.getElementById('margin-laba-display').textContent = marginLaba.toFixed(2) + '%';
            document.getElementById('stok-tersedia-display').textContent = stock + ' paket';
            
            // Hitung total
            hitungTotal();
        });

        // Event: Jumlah Paket berubah
        jumlahPcsInput.addEventListener('input', function() {
            const selectedOption = produkSelect.options[produkSelect.selectedIndex];
            const stokPaket = parseInt(selectedOption?.dataset?.stok) || 0;
            const value = parseInt(this.value) || 0;
            const warning = document.getElementById('stok-warning');
            const warningText = document.getElementById('stok-warning-text');
            const btnSimpan = document.getElementById('btn-simpan');
            
            if (stokPaket > 0 && value > stokPaket) {
                // Tampilkan peringatan stok tidak cukup
                warning.style.display = 'block';
                warningText.textContent = `Stok tidak mencukupi! Anda memasukkan ${value} paket, tetapi stok tersedia hanya ${stokPaket} paket. Kurangi jumlah untuk melanjutkan.`;
                this.classList.add('is-invalid');
                btnSimpan.disabled = true;
                btnSimpan.classList.replace('btn-primary', 'btn-secondary');
            } else {
                warning.style.display = 'none';
                this.classList.remove('is-invalid');
                btnSimpan.disabled = false;
                btnSimpan.classList.replace('btn-secondary', 'btn-primary');
            }
            
            hitungTotal();
        });

        // Event: Diskon berubah
        diskonInput.addEventListener('input', function() {
            hitungTotal();
        });

        // Event: Tipe diskon berubah
        tipeDiskonSelect.addEventListener('change', function() {
            hitungTotal();
        });

        // Event: Ongkir berubah
        ongkirInput.addEventListener('input', function() {
            hitungTotal();
        });

        // Trigger calculation jika produk sudah dipilih saat load
        if (produkSelect.value) {
            produkSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
