@extends('layouts.app')

@section('title', 'Edit Kategori - Laryn')
@section('page-title', 'Edit Kategori Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Kategori: {{ $category->nama_kategori }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori', $category->nama_kategori) }}" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Jenis Kategori <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input @error('jenis_kategori') is-invalid @enderror" type="radio" 
                                       name="jenis_kategori" id="jenis_produk" value="produk" 
                                       {{ old('jenis_kategori', $category->jenis_kategori) === 'produk' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="jenis_produk">
                                    <strong>Bahan Baku</strong>
                                    <small class="d-block text-muted">Untuk makanan, bahan baku, produk yang dijual</small>
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input @error('jenis_kategori') is-invalid @enderror" type="radio" 
                                       name="jenis_kategori" id="jenis_peralatan" value="peralatan" 
                                       {{ old('jenis_kategori', $category->jenis_kategori) === 'peralatan' ? 'checked' : '' }} required>
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
                                      rows="4">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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
