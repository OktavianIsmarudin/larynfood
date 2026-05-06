@extends('layouts.app')

@section('title', 'Edit Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Pembelian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembelian.update', $pembelian) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $pembelian->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $pembelian->category_id) == $category->id ? 'selected' : '' }}>
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
                                       id="tanggal_pembelian" name="tanggal_pembelian" value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}" required>
                                @error('tanggal_pembelian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                                       id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $pembelian->nama_produk) }}" 
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
                                       id="qty" name="qty" value="{{ old('qty', $pembelian->qty) }}" min="1" required>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('harga_satuan') is-invalid @enderror" 
                                       id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan', $pembelian->harga_satuan) }}" 
                                       min="0" step="0.01" required>
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
                            <input type="hidden" id="subtotal_hidden" name="subtotal" value="{{ old('subtotal', $pembelian->subtotal ?? 0) }}">
                            <small class="text-muted">Otomatis: Jumlah Barang × Harga Satuan</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipe_diskon" class="form-label">Tipe Diskon</label>
                                <select class="form-select @error('tipe_diskon') is-invalid @enderror" id="tipe_diskon" name="tipe_diskon">
                                    <option value="" {{ old('tipe_diskon', $pembelian->tipe_diskon) == '' ? 'selected' : '' }}>Tanpa Diskon</option>
                                    <option value="persen" {{ old('tipe_diskon', $pembelian->tipe_diskon) == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                                    <option value="nominal" {{ old('tipe_diskon', $pembelian->tipe_diskon) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                                @error('tipe_diskon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="diskon" class="form-label">Nilai Diskon</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('diskon') is-invalid @enderror" 
                                           id="diskon" name="diskon" value="{{ old('diskon', $pembelian->diskon ?? 0) }}" 
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
                            <input type="hidden" id="total_pembelian_hidden" name="total_pengeluaran" value="{{ $pembelian->total_pengeluaran }}">
                            <small class="text-muted">Otomatis: Subtotal − Diskon</small>
                        </div>

                        <div class="mb-3">
                            <label for="bukti_pembelian" class="form-label">Bukti Pembelian (Opsional)</label>
                            @if ($pembelian->bukti_pembelian)
                                <div class="alert alert-info mb-2">
                                    <small>📎 File saat ini: <strong>{{ basename($pembelian->bukti_pembelian) }}</strong></small>
                                </div>
                            @endif
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
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Perbarui
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

    // Initial calculation if values exist
    calculateTotal();
});
</script>
@endsection
