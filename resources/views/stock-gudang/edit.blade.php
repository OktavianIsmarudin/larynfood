@extends('layouts.app')

@section('title', 'Edit Stock Gudang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Stock Gudang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-gudang.update', $stockGudang) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $stockGudang->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" 
                                       id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $stockGudang->nama_produk) }}" required>
                                @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $stockGudang->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $stockGudang->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_pack" class="form-label">Jumlah Pack <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jumlah_pack') is-invalid @enderror" 
                                       id="jumlah_pack" name="jumlah_pack" value="{{ old('jumlah_pack', $stockGudang->jumlah_pack ?? $stockGudang->jumlah_stock) }}" min="0" required>
                                @error('jumlah_pack')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror" 
                                       id="satuan" name="satuan" value="{{ old('satuan', $stockGudang->satuan ?? $stockGudang->satuan_utama) }}" required>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="konversi_satuan" class="form-label">Konversi Satuan</label>
                                <input type="number" class="form-control @error('konversi_satuan') is-invalid @enderror" 
                                       id="konversi_satuan" name="konversi_satuan" value="{{ old('konversi_satuan', $stockGudang->konversi_satuan) }}" min="1">
                                @error('konversi_satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="harga_beli_pack" class="form-label">Harga Beli/Pack</label>
                            <input type="number" class="form-control @error('harga_beli_pack') is-invalid @enderror" 
                                   id="harga_beli_pack" name="harga_beli_pack" value="{{ old('harga_beli_pack', $stockGudang->harga_beli_pack ?? $stockGudang->harga_beli) }}" min="0" step="0.01">
                            @error('harga_beli_pack')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                            <a href="{{ route('stock-gudang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
