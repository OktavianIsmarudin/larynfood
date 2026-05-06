@extends('layouts.app')

@section('title', 'Detail Bill of Material')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('bom.index') }}" style="color: #6B7280; text-decoration: none; display: flex; align-items: center; font-size: 14px; margin-bottom: 8px;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Daftar BOM
                    </a>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-file-recipe" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> {{ $bom->nama_paket }}
                    </h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('produk-paket.edit', $bom->id) }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none;">
                        <i class="fas fa-edit me-2"></i> Edit BOM
                    </a>
                    <a href="{{ route('bom.export-bom', $bom->id) }}" class="btn fw-bold" style="background-color: #10B981; color: white; padding: 12px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none;">
                        <i class="fas fa-download me-2"></i> Export
                    </a>
                </div>
            </div>

            {{-- INFO CARD --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                        <div class="card-body" style="padding: 20px;">
                            <div style="font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 8px; text-transform: uppercase;">Kode BOM</div>
                            <div style="font-size: 18px; font-weight: 700; color: #1A1A1A;">
                                <code style="background-color: #F3F4F6; padding: 6px 10px; border-radius: 4px;">{{ $bom->kode_paket ?? '-' }}</code>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                        <div class="card-body" style="padding: 20px;">
                            <div style="font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 8px; text-transform: uppercase;">Total Item</div>
                            <div style="font-size: 24px; font-weight: 700; color: #4F46E5;">
                                {{ $bom->details->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                        <div class="card-body" style="padding: 20px;">
                            <div style="font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 8px; text-transform: uppercase;">HPP Total</div>
                            <div style="font-size: 20px; font-weight: 700; color: #166534;">
                                Rp {{ number_format($bom->hpp_total ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                        <div class="card-body" style="padding: 20px;">
                            <div style="font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 8px; text-transform: uppercase;">Status</div>
                            <div style="font-size: 14px; font-weight: 600;">
                                @if ($bom->status == 'aktif')
                                    <span style="background-color: #D1FAE5; color: #065F46; padding: 6px 12px; border-radius: 6px;">
                                        <i class="fas fa-check-circle me-1"></i> Aktif
                                    </span>
                                @else
                                    <span style="background-color: #FEE2E2; color: #991B1B; padding: 6px 12px; border-radius: 6px;">
                                        <i class="fas fa-times-circle me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            @if ($bom->deskripsi)
                <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                    <div class="card-body" style="padding: 20px;">
                        <h6 style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; margin-bottom: 12px;">Deskripsi</h6>
                        <p style="color: #495057; margin: 0; line-height: 1.6;">{{ $bom->deskripsi }}</p>
                    </div>
                </div>
            @endif

            {{-- KOMPONEN / ITEMS --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 20px; border-radius: 12px 12px 0 0;">
                    <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                        <i class="fas fa-list" style="color: #F59E0B; margin-right: 8px;"></i> Komponen BOM
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($bom->details->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 2px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; width: 40px;">#</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Item / Material</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Satuan</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Qty/Paket</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">HPP/Unit</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Subtotal</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $nomor = 1; @endphp
                                    @foreach ($bom->details as $detail)
                                        <tr style="border-bottom: 1px solid #E5E7EB;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='white';">
                                            <td style="padding: 16px 20px; color: #6B7280; font-weight: 500;">{{ $nomor++ }}</td>
                                            <td style="padding: 16px 20px;">
                                                <div style="color: #1A1A1A; font-weight: 600;">{{ $detail->stockGudang->nama_produk ?? 'N/A' }}</div>
                                                <small style="color: #6B7280;">{{ $detail->stockGudang->category->nama_kategori ?? '-' }}</small>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center; color: #6B7280;">
                                                <code style="background-color: #F3F4F6; padding: 4px 8px; border-radius: 4px;">{{ $detail->stockGudang->satuan ?? '-' }}</code>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 600;">
                                                {{ number_format($detail->qty_per_paket, 2, ',', '.') }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #166534; font-weight: 600;">
                                                Rp {{ number_format($detail->stockGudang->hpp_per_pcs ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; font-weight: 700; color: #8B5CF6;">
                                                Rp {{ number_format(($detail->qty_per_paket * ($detail->stockGudang->hpp_per_pcs ?? 0)), 0, ',', '.') }}
                                            </td>
                                            <td style="padding: 16px 20px; color: #6B7280;">
                                                {{ $detail->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #F0F9FF; border-top: 2px solid #8B5CF6;">
                                        <td colspan="5" style="padding: 20px; text-align: right; font-weight: 700; color: #1A1A1A; font-size: 16px;">Total HPP Paket:</td>
                                        <td style="padding: 20px; text-align: right; font-weight: 700; color: #8B5CF6; font-size: 18px;">
                                            Rp {{ number_format($bom->hpp_total ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 20px;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div style="padding: 40px 20px; text-align: center;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 16px; display: block;"></i>
                            <p style="color: #6B7280; font-size: 16px;">Belum ada komponen dalam BOM ini</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PRODUK SIAP JUAL YANG MENGGUNAKAN BOM INI --}}
            @if ($bom->produkSiapJuals && $bom->produkSiapJuals->count() > 0)
                <div class="card border-0 mt-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                    <div class="card-header" style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB; padding: 20px; border-radius: 12px 12px 0 0;">
                        <h5 style="margin: 0; font-weight: 600; color: #1A1A1A;">
                            <i class="fas fa-box-open" style="color: #F59E0B; margin-right: 8px;"></i> Produk Siap Jual yang Menggunakan BOM Ini
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Produk</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Stock</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Harga</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bom->produkSiapJuals as $produk)
                                        <tr style="border-bottom: 1px solid #E5E7EB;">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">{{ $produk->nama_produk }}</td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <span style="background-color: #E0E7FF; color: #4F46E5; padding: 4px 12px; border-radius: 16px; font-size: 12px;">{{ $produk->stok_siap_jual }} pcs</span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #166534; font-weight: 600;">
                                                Rp {{ number_format($produk->harga_jual ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                @if ($produk->is_published == 1)
                                                    <span style="background-color: #D1FAE5; color: #065F46; padding: 4px 12px; border-radius: 4px; font-size: 12px;">Published</span>
                                                @else
                                                    <span style="background-color: #FEE2E2; color: #991B1B; padding: 4px 12px; border-radius: 4px; font-size: 12px;">Draft</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- BACK BUTTON --}}
            <div class="mt-4">
                <a href="{{ route('bom.index') }}" class="btn" style="background-color: #E5E7EB; color: #374151; padding: 12px 24px; border-radius: 8px; border: none; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
