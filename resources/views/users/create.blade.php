@extends('layouts.app')

@section('title', 'Tambah User Admin')
@section('page-title', 'Tambah User Admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-user-plus" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Tambah User Admin Baru
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Buat akun admin atau super admin baru</small>
                </div>
            </div>

            {{-- CARD FORM --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label" style="font-weight: 600; color: #374151; font-size: 14px;">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label" style="font-weight: 600; color: #374151; font-size: 14px;">Email <span style="color: #EF4444;">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label" style="font-weight: 600; color: #374151; font-size: 14px;">Role <span style="color: #EF4444;">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Admin: Kelola inventory & transaksi sendiri | Customer: Pembeli yang bisa pesan produk</small>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label" style="font-weight: 600; color: #374151; font-size: 14px;">Password <span style="color: #EF4444;">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label" style="font-weight: 600; color: #374151; font-size: 14px;">Konfirmasi Password <span style="color: #EF4444;">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary" style="padding: 12px 24px; font-size: 14px; border-radius: 8px;">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn" style="background-color: #F59E0B; color: white; padding: 12px 32px; font-size: 14px; border-radius: 8px; border: none;">
                                <i class="fas fa-save me-2"></i> Simpan User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
