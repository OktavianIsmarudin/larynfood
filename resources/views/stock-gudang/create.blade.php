@extends('layouts.app')

@section('title', 'Tambah Stock Gudang')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                {{ $pembelian ? 'Masukkan Stock dari Pembelian' : 'Tambah Stock Gudang Baru' }}
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('stock-gudang.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Terjadi kesalahan:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock-gudang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Hidden purchase_id jika dari pembelian --}}
                @if ($pembelian)
                    <input type="hidden" name="purchase_id" value="{{ $pembelian->id }}">
                @endif

                {{-- Section 1: Info Pembelian (readonly jika dari pembelian) --}}
                @if ($pembelian)
                    <div class="card mb-4 bg-light">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informasi Pembelian (Autofill)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pembelian</label>
                                    <input type="text" class="form-control" disabled 
                                        value="{{ $pembelian->tanggal_pembelian->format('d M Y') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Supplier</label>
                                    <input type="text" class="form-control" disabled 
                                        value="{{ $pembelian->supplier->nama ?? '-' }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" disabled 
                                        value="{{ $pembelian->nama_produk }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <input type="text" class="form-control" disabled 
                                        value="{{ $pembelian->category->nama_kategori ?? '-' }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jumlah Pack</label>
                                    <input type="text" class="form-control" disabled 
                                        value="{{ $pembelian->qty }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Harga/Pack</label>
                                    <input type="text" class="form-control" disabled 
                                        value="Rp {{ number_format($pembelian->harga_satuan, 0, ',', '.') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control" disabled 
                                        value="Rp {{ number_format($pembelian->total_pengeluaran, 0, ',', '.') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section 2: Data Input Pengguna --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            Data {{ $pembelian ? 'Produk/Unit' : 'Stock Gudang' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- SKU --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                    id="sku" name="sku" value="{{ old('sku') }}" required
                                    placeholder="Contoh: PROD-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div class="col-md-6">
                                <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror" 
                                    id="satuan" name="satuan" value="{{ old('satuan', 'BOX') }}" required
                                    placeholder="Contoh: BOX, PACK, KARUNG">
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Konversi Satuan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="konversi_satuan" class="form-label">Konversi Satuan (PCS/Pack) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('konversi_satuan') is-invalid @enderror" 
                                    id="konversi_satuan" name="konversi_satuan" 
                                    value="{{ old('konversi_satuan') }}" required min="1"
                                    onchange="calculateTotalPcs()"
                                    placeholder="Contoh: 25">
                                <small class="text-muted">1 Pack = berapa PCS?</small>
                                @error('konversi_satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nama Produk (jika tidak dari pembelian) --}}
                            @if (!$pembelian)
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                                        id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required
                                        placeholder="Nama produk">
                                    @error('nama_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="nama_produk" value="{{ $pembelian->nama_produk }}">
                            @endif
                        </div>

                        {{-- Lokasi Gudang --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lokasi_gudang" class="form-label">Lokasi Gudang</label>
                                <input type="text" class="form-control @error('lokasi_gudang') is-invalid @enderror" 
                                    id="lokasi_gudang" name="lokasi_gudang" value="{{ old('lokasi_gudang') }}"
                                    placeholder="Contoh: Rak A-1, Sudut Timur">
                                @error('lokasi_gudang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            @if (old('category_id') == $category->id || $pembelian?->category_id == $category->id) selected @endif>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Supplier --}}
                        @if (!$pembelian)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" 
                                                @if (old('supplier_id') == $supplier->id) selected @endif>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="supplier_id" value="{{ $pembelian->supplier_id }}">
                        @endif
                    </div>
                </div>

                {{-- Section 3: Ringkasan Perhitungan --}}
                <div class="card mb-4 bg-light">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Ringkasan Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total PCS</label>
                                <input type="text" class="form-control" id="totalPcs" disabled 
                                    value="0">
                                <small class="text-muted">Dihitung otomatis: Jumlah Pack × Konversi Satuan</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">PCS Terpakai</label>
                                <input type="text" class="form-control" id="pcsTerpakai" disabled 
                                    value="0">
                                <small class="text-muted">Stock yang telah terjual</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">PCS Sisa</label>
                                <input type="text" class="form-control" id="pcsSisa" disabled 
                                    value="0">
                                <small class="text-muted">Stock yang tersedia</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">HPP/PCS</label>
                                <input type="text" class="form-control" id="hppPerPcs" disabled 
                                    value="Rp 0">
                                <small class="text-muted">Harga Pokok per PCS</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Simpan Stock Gudang
                        </button>
                        <a href="{{ route('stock-gudang.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateTotalPcs() {
    const konversiSatuan = parseInt(document.getElementById('konversi_satuan').value) || 0;
    let jumlahPack = 1;
    
    @if ($pembelian)
        jumlahPack = {{ $pembelian->qty }};
    @endif
    
    const totalPcs = jumlahPack * konversiSatuan;
    const pcsSisa = totalPcs; // Initially, sisa = total
    
    // Update display
    document.getElementById('totalPcs').value = totalPcs.toLocaleString('id-ID');
    document.getElementById('pcsSisa').value = pcsSisa.toLocaleString('id-ID');
    document.getElementById('pcsTerpakai').value = '0';
    
    @if ($pembelian)
        // Calculate HPP per PCS
        const hargaBeli = {{ $pembelian->harga_satuan }};
        const hppPerPcs = hargaBeli / konversiSatuan;
        document.getElementById('hppPerPcs').value = 'Rp ' + hppPerPcs.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    @endif
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', calculateTotalPcs);
</script>

@endsection
