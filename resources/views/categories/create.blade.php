@extends('layouts.app')

@section('title', 'Tambah Kategori - Laryn')
@section('page-title', 'Tambah Kategori Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Kategori Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori') }}" placeholder="Contoh: Dimsum, Kue Basah, Tray, dll" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Jenis Kategori <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input @error('jenis_kategori') is-invalid @enderror" type="radio" 
                                       name="jenis_kategori" id="jenis_produk" value="produk" 
                                       {{ old('jenis_kategori', 'produk') === 'produk' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis_produk">
                                    <strong>Bahan Baku</strong>
                                    <small class="d-block text-muted">Untuk makanan, bahan baku, produk yang dijual</small>
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input @error('jenis_kategori') is-invalid @enderror" type="radio" 
                                       name="jenis_kategori" id="jenis_peralatan" value="peralatan" 
                                       {{ old('jenis_kategori') === 'peralatan' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis_peralatan">
                                    <strong>Peralatan / Kemasan</strong>
                                    <small class="d-block text-muted">Untuk tray, plastik, box, peralatan inventory</small>
                                </label>
                            </div>
                            @error('jenis_kategori')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                      rows="4" placeholder="Deskripsi singkat tentang kategori ini...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
