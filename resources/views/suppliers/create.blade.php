@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Supplier Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_supplier') is-invalid @enderror" 
                                   id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier') }}" required>
                            @error('nama_supplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kontak" class="form-label">Kontak Person</label>
                            <input type="text" class="form-control @error('kontak') is-invalid @enderror" 
                                   id="kontak" name="kontak" value="{{ old('kontak') }}">
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" 
                                   id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="0812345678">
                            @error('nomor_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kota" class="form-label">Kota</label>
                            <input type="text" class="form-control @error('kota') is-invalid @enderror" 
                                   id="kota" name="kota" value="{{ old('kota') }}">
                            @error('kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
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
