@extends('layouts.app')

@section('title', 'Nilai Gizi - Laryn')
@section('page-title', 'Nilai Gizi')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-seedling" style="color: #10b981; margin-right: 12px; opacity: 0.8;"></i> Nilai Gizi Produk
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola informasi nilai gizi per PCS item gudang</small>
                </div>
            </div>

            {{-- INFO CARD --}}
            <div class="alert mb-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px 20px;">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle mt-1" style="color: #059669; margin-right: 12px; font-size: 18px;"></i>
                    <div>
                        <strong style="color: #065f46;">Catatan:</strong>
                        <span style="color: #047857;">Nilai gizi yang diisi di sini adalah per <strong>1 PCS</strong> item gudang. Perhitungan gizi untuk produk paket/platter akan dihitung otomatis berdasarkan komposisi paket.</span>
                    </div>
                </div>
            </div>

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #D1FAE5; background-color: #F0FDF4;">
                    <i class="fas fa-check-circle" style="color: #22C55E; margin-right: 8px;"></i> <strong>Sukses!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- CARD TABEL --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-0">
                    @if ($stocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">No</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama Item</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Kategori</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Energi (kkal)</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Protein (g)</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Lemak (g)</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Karbohidrat (g)</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Status</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stocks as $index => $stock)
                                        @php
                                            $hasNutrition = !is_null($stock->energi_kkal) || !is_null($stock->protein_g) || !is_null($stock->lemak_g) || !is_null($stock->karbohidrat_g);
                                        @endphp
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                            <td style="padding: 16px 20px; color: #6B7280;">{{ $index + 1 }}</td>
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 600;">
                                                {{ $stock->nama_produk ?? '-' }}
                                            </td>
                                            <td style="padding: 16px 20px; color: #6B7280;">
                                                <span style="background-color: #F3F4F6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ $stock->category->nama_kategori ?? '-' }}
                                                </span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center; font-weight: 500; color: {{ !is_null($stock->energi_kkal) ? '#1A1A1A' : '#D1D5DB' }};">
                                                {{ !is_null($stock->energi_kkal) ? number_format($stock->energi_kkal, 2) : '—' }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center; font-weight: 500; color: {{ !is_null($stock->protein_g) ? '#1A1A1A' : '#D1D5DB' }};">
                                                {{ !is_null($stock->protein_g) ? number_format($stock->protein_g, 2) : '—' }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center; font-weight: 500; color: {{ !is_null($stock->lemak_g) ? '#1A1A1A' : '#D1D5DB' }};">
                                                {{ !is_null($stock->lemak_g) ? number_format($stock->lemak_g, 2) : '—' }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center; font-weight: 500; color: {{ !is_null($stock->karbohidrat_g) ? '#1A1A1A' : '#D1D5DB' }};">
                                                {{ !is_null($stock->karbohidrat_g) ? number_format($stock->karbohidrat_g, 2) : '—' }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                @if($hasNutrition)
                                                    <span style="background-color: #D1FAE5; color: #065F46; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-check-circle" style="margin-right: 4px;"></i> Terisi
                                                    </span>
                                                @else
                                                    <span style="background-color: #FEF3C7; color: #92400E; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> Belum
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('nilai-gizi.show', $stock->id) }}" class="btn btn-sm" style="background-color: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 500;" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('nilai-gizi.edit', $stock->id) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 500;" title="Edit Nilai Gizi">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-seedling" style="font-size: 48px; color: #D1D5DB; margin-bottom: 16px; display: block;"></i>
                            <p style="color: #6B7280; font-size: 16px; margin: 0;">Belum ada item di Stock Gudang</p>
                            <small style="color: #9CA3AF;">Tambahkan item melalui menu Stock Gudang terlebih dahulu</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SUMMARY --}}
            @if($stocks->count() > 0)
                @php
                    $filled = $stocks->filter(fn($s) => !is_null($s->energi_kkal) || !is_null($s->protein_g) || !is_null($s->lemak_g) || !is_null($s->karbohidrat_g))->count();
                    $total = $stocks->count();
                    $pct = $total > 0 ? round(($filled / $total) * 100) : 0;
                @endphp
                <div class="mt-3 d-flex align-items-center gap-3" style="color: #6B7280; font-size: 13px;">
                    <span><i class="fas fa-chart-pie" style="margin-right: 4px;"></i> Progress pengisian: <strong>{{ $filled }}/{{ $total }}</strong> item ({{ $pct }}%)</span>
                    <div style="flex: 1; max-width: 200px; height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 3px; transition: width 0.3s;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
