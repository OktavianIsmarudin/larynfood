@extends('layouts.app')

@section('title', 'Edit Nilai Gizi - ' . $stockGudang->nama_produk)
@section('page-title', 'Edit Nilai Gizi')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 28px; margin: 0;">
                        <i class="fas fa-edit" style="color: #f59e0b; margin-right: 12px; opacity: 0.8;"></i> Edit Nilai Gizi
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Perbarui informasi nutrisi per 1 PCS</small>
                </div>
                <a href="{{ route('nilai-gizi.index') }}" class="btn fw-bold" style="background-color: #6B7280; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none;">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            {{-- PRODUCT HEADER CARD --}}
            <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <div style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 12px; padding: 20px 24px; display: flex; align-items: center; gap: 16px;">
                    <div style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box" style="color: white; font-size: 22px;"></i>
                    </div>
                    <div>
                        <h5 style="color: white; font-weight: 700; margin: 0; font-size: 18px;">{{ $stockGudang->nama_produk }}</h5>
                        <small style="color: rgba(255,255,255,0.8);">SKU: {{ $stockGudang->sku ?? '-' }} | Kategori: {{ $stockGudang->category->nama_kategori ?? '-' }}</small>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <div class="card-body" style="padding: 28px;">
                    <form action="{{ route('nilai-gizi.update', $stockGudang->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Energi --}}
                            <div class="col-md-6">
                                <label for="energi_kkal" class="form-label" style="font-weight: 600; color: #374151;">
                                    <i class="fas fa-fire" style="color: #f59e0b; margin-right: 6px;"></i> Energi (kkal / pcs)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('energi_kkal') is-invalid @enderror"
                                        id="energi_kkal" name="energi_kkal"
                                        value="{{ old('energi_kkal', $stockGudang->energi_kkal) }}"
                                        placeholder="Contoh: 25.50"
                                        style="border-radius: 8px 0 0 8px; padding: 12px 16px;">
                                    <span class="input-group-text" style="background-color: #FEF3C7; color: #92400E; font-weight: 600; border-radius: 0 8px 8px 0; border-color: #FDE68A;">kkal</span>
                                </div>
                                @error('energi_kkal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Protein --}}
                            <div class="col-md-6">
                                <label for="protein_g" class="form-label" style="font-weight: 600; color: #374151;">
                                    <i class="fas fa-dna" style="color: #3b82f6; margin-right: 6px;"></i> Protein (g / pcs)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('protein_g') is-invalid @enderror"
                                        id="protein_g" name="protein_g"
                                        value="{{ old('protein_g', $stockGudang->protein_g) }}"
                                        placeholder="Contoh: 3.20"
                                        style="border-radius: 8px 0 0 8px; padding: 12px 16px;">
                                    <span class="input-group-text" style="background-color: #DBEAFE; color: #1E40AF; font-weight: 600; border-radius: 0 8px 8px 0; border-color: #93C5FD;">g</span>
                                </div>
                                @error('protein_g')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Lemak --}}
                            <div class="col-md-6">
                                <label for="lemak_g" class="form-label" style="font-weight: 600; color: #374151;">
                                    <i class="fas fa-droplet" style="color: #ec4899; margin-right: 6px;"></i> Lemak (g / pcs)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('lemak_g') is-invalid @enderror"
                                        id="lemak_g" name="lemak_g"
                                        value="{{ old('lemak_g', $stockGudang->lemak_g) }}"
                                        placeholder="Contoh: 1.80"
                                        style="border-radius: 8px 0 0 8px; padding: 12px 16px;">
                                    <span class="input-group-text" style="background-color: #FCE7F3; color: #9D174D; font-weight: 600; border-radius: 0 8px 8px 0; border-color: #F9A8D4;">g</span>
                                </div>
                                @error('lemak_g')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Karbohidrat --}}
                            <div class="col-md-6">
                                <label for="karbohidrat_g" class="form-label" style="font-weight: 600; color: #374151;">
                                    <i class="fas fa-wheat-awn" style="color: #10b981; margin-right: 6px;"></i> Karbohidrat (g / pcs)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('karbohidrat_g') is-invalid @enderror"
                                        id="karbohidrat_g" name="karbohidrat_g"
                                        value="{{ old('karbohidrat_g', $stockGudang->karbohidrat_g) }}"
                                        placeholder="Contoh: 4.50"
                                        style="border-radius: 8px 0 0 8px; padding: 12px 16px;">
                                    <span class="input-group-text" style="background-color: #D1FAE5; color: #065F46; font-weight: 600; border-radius: 0 8px 8px 0; border-color: #6EE7B7;">g</span>
                                </div>
                                @error('karbohidrat_g')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Info Note --}}
                        <div class="alert mt-4 mb-4" style="background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 14px 18px;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle mt-1" style="color: #3B82F6; margin-right: 10px;"></i>
                                <small style="color: #1E40AF;">
                                    Nilai gizi per <strong>1 PCS</strong>. Kosongkan field jika belum diketahui. Perubahan ini <strong>tidak mempengaruhi</strong> stok, harga, atau transaksi yang sudah ada.
                                </small>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn fw-bold" style="background-color: #10b981; color: white; padding: 12px 28px; font-size: 14px; border-radius: 8px; border: none;">
                                <i class="fas fa-save me-2"></i> Simpan Nilai Gizi
                            </button>
                            <a href="{{ route('nilai-gizi.index') }}" class="btn fw-bold" style="background-color: #F3F4F6; color: #374151; padding: 12px 28px; font-size: 14px; border-radius: 8px; border: 1px solid #D1D5DB;">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
