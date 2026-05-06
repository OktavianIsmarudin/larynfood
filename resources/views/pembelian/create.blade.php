@extends('layouts.app')

@section('title', 'Tambah Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Pembelian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembelian.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pembelian" class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_pembelian') is-invalid @enderror" 
                                       id="tanggal_pembelian" name="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}" required>
                                @error('tanggal_pembelian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nomor_bukti_pembelian" class="form-label">Nomor Bukti Pembelian</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('nomor_bukti_pembelian') is-invalid @enderror" 
                                           id="nomor_bukti_pembelian" name="nomor_bukti_pembelian" 
                                           value="{{ old('nomor_bukti_pembelian') }}" 
                                           placeholder="Format: INV-2026-001 (otomatis jika kosong)">
                                    <button type="button" class="btn btn-outline-secondary" id="btn_auto_nomor" title="Generate Otomatis">
                                        <i class="fas fa-magic"></i> Auto
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">Biarkan kosong untuk auto-generate format INV-YYYY-NNN</small>
                                @error('nomor_bukti_pembelian')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                                       id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" 
                                       placeholder="Contoh: Daging Ayam, Minyak Goreng, dll" required>
                                @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="qty" class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('qty') is-invalid @enderror" 
                                       id="qty" name="qty" value="{{ old('qty') }}" min="1" required>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('harga_satuan') is-invalid @enderror" 
                                       id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}" 
                                       min="0" step="0.01" placeholder="Rp" required>
                                @error('harga_satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subtotal_display" class="form-label">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" 
                                       id="subtotal_display" readonly style="background-color: #f0f0f0;" placeholder="0">
                            </div>
                            <input type="hidden" id="subtotal_hidden" name="subtotal" value="0">
                            <small class="text-muted">Otomatis: Jumlah Barang × Harga Satuan</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipe_diskon" class="form-label">Tipe Diskon</label>
                                <select class="form-select @error('tipe_diskon') is-invalid @enderror" id="tipe_diskon" name="tipe_diskon">
                                    <option value="" {{ old('tipe_diskon') == '' ? 'selected' : '' }}>Tanpa Diskon</option>
                                    <option value="persen" {{ old('tipe_diskon') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                                    <option value="nominal" {{ old('tipe_diskon') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="diskon" class="form-label">Nilai Diskon</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('diskon') is-invalid @enderror" 
                                           id="diskon" name="diskon" value="{{ old('diskon', 0) }}" 
                                           min="0" step="0.01" placeholder="0">
                                    <span class="input-group-text" id="diskon_suffix">-</span>
                                </div>
                                @error('diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Potongan Diskon</label>
                                <div class="input-group">
                                    <span class="input-group-text">- Rp</span>
                                    <input type="text" class="form-control" 
                                           id="diskon_amount_display" readonly style="background-color: #f0f0f0; color: #DC3545;" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="total_pembelian" class="form-label">Total Pembelian <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" 
                                       id="total_pembelian" readonly style="background-color: #f0f0f0; font-weight: bold; font-size: 1.1em; color: #1D4ED8;" placeholder="0">
                            </div>
                            <input type="hidden" id="total_pembelian_hidden" name="total_pengeluaran" value="0">
                            <small class="text-muted">Otomatis: Subtotal − Diskon</small>
                        </div>

                        <div class="mb-3">
                            <label for="bukti_pembelian" class="form-label">Bukti Pembelian (Opsional)</label>
                            <input type="file" class="form-control @error('bukti_pembelian') is-invalid @enderror" 
                                   id="bukti_pembelian" name="bukti_pembelian" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted d-block mt-1">
                                📎 Format: PDF, JPG, JPEG, PNG | Maksimal: 1 MB
                                <br>💡 Upload foto invoice, kwitansi, atau bukti pembayaran
                            </small>
                            @error('bukti_pembelian')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
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
    const qty = document.getElementById('qty');
    const hargaSatuan = document.getElementById('harga_satuan');
    const totalPembelian = document.getElementById('total_pembelian');
    const totalPembelianHidden = document.getElementById('total_pembelian_hidden');
    const btnAutoNomor = document.getElementById('btn_auto_nomor');
    const nomorBukti = document.getElementById('nomor_bukti_pembelian');

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }

    const tipeDiskon = document.getElementById('tipe_diskon');
    const diskon = document.getElementById('diskon');
    const diskonSuffix = document.getElementById('diskon_suffix');
    const diskonAmountDisplay = document.getElementById('diskon_amount_display');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const subtotalHidden = document.getElementById('subtotal_hidden');

    function calculateTotal() {
        const qtyVal = parseFloat(qty.value) || 0;
        const price = parseFloat(hargaSatuan.value) || 0;
        const subtotal = qtyVal * price;

        // Update subtotal display
        subtotalDisplay.value = formatCurrency(subtotal);
        subtotalHidden.value = subtotal;

        // Hitung diskon
        const tipe = tipeDiskon.value;
        const nilaiDiskon = parseFloat(diskon.value) || 0;
        let potongan = 0;

        if (tipe === 'persen') {
            potongan = subtotal * (nilaiDiskon / 100);
            diskonSuffix.textContent = '%';
        } else if (tipe === 'nominal') {
            potongan = nilaiDiskon;
            diskonSuffix.textContent = 'Rp';
        } else {
            potongan = 0;
            diskonSuffix.textContent = '-';
            diskon.value = 0;
        }

        // Pastikan potongan tidak melebihi subtotal
        if (potongan > subtotal) potongan = subtotal;

        diskonAmountDisplay.value = formatCurrency(potongan);

        const total = Math.max(0, subtotal - potongan);

        // Update display
        totalPembelian.value = formatCurrency(total);
        // Update hidden field for form submission
        totalPembelianHidden.value = total;
    }

    // Event listeners for real-time calculation
    qty.addEventListener('input', calculateTotal);
    qty.addEventListener('change', calculateTotal);
    hargaSatuan.addEventListener('input', calculateTotal);
    hargaSatuan.addEventListener('change', calculateTotal);
    tipeDiskon.addEventListener('change', function() {
        if (this.value === '') {
            diskon.value = 0;
            diskon.disabled = true;
        } else {
            diskon.disabled = false;
        }
        calculateTotal();
    });
    diskon.addEventListener('input', calculateTotal);
    diskon.addEventListener('change', calculateTotal);

    // Initial state
    if (!tipeDiskon.value) diskon.disabled = true;

    // Auto-generate nomor bukti
    btnAutoNomor.addEventListener('click', function() {
        const tahun = new Date().getFullYear();
        // Generate simple format since we can't query database from frontend
        // The server will validate and regenerate if needed
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        nomorBukti.value = `INV-${tahun}-${random}`;
        nomorBukti.focus();
    });

    // Auto-clear field jika user ingin automatic generation
    nomorBukti.addEventListener('focus', function() {
        if (this.value && this.value.match(/^INV-\d{4}-\d{3}$/)) {
            // Already in correct format
        }
    });

    // Initial calculation if values exist
    calculateTotal();
});
</script>
@endsection
