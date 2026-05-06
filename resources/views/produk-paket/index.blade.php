@extends('layouts.app')

@section('title', 'Produk Paket/Platter')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-layer-group" style="color: #8B5CF6; margin-right: 12px; opacity: 0.8;"></i> Produk Paket/Platter
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola komposisi paket. Untuk tampil di guest, paket harus dibuat dulu ke Produk Siap Jual lalu dipublikasikan.</small>
                </div>
                <a href="{{ route('produk-paket.create') }}" class="btn fw-bold" style="background-color: #8B5CF6; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#7C3AED'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.3)';" onmouseout="this.style.backgroundColor='#8B5CF6'; this.style.boxShadow='0 2px 8px rgba(139, 92, 246, 0.2)';">
                    <i class="fas fa-plus me-2"></i> Buat Paket Baru
                </a>
            </div>

            {{-- ALERTS --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #D1FAE5; background-color: #F0FDF4;">
                    <i class="fas fa-check-circle" style="color: #22C55E; margin-right: 8px;"></i> <strong>Sukses!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #FEE2E2; background-color: #FEF2F2;">
                    <i class="fas fa-exclamation-circle" style="color: #DC3545; margin-right: 8px;"></i> <strong>Error!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- CARD TABEL --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-0">
                    @if ($pakets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama Paket</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Kode</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Jumlah Item</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">HPP Total</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Produk Siap Jual</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Status</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pakets as $paket)
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                                {{ $paket->nama_paket }}
                                                @if($paket->deskripsi)
                                                    <br><small style="color: #9CA3AF;">{{ Str::limit($paket->deskripsi, 50) }}</small>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px;">
                                                @if($paket->kode_paket)
                                                    <span style="background-color: #E8E8FF; color: #5B21B6; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ $paket->kode_paket }}</span>
                                                @else
                                                    <span style="color: #9CA3AF;">-</span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <span style="background-color: #E0F2FE; color: #0369A1; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ $paket->details_count }} item</span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">Rp {{ number_format($paket->hpp_total ?? 0, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <span style="background-color: #DCFCE7; color: #166534; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">{{ (int) ($paket->produk_siap_juals_sum_stok_siap_jual ?? 0) }} paket</span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                @if($paket->status === 'aktif')
                                                    <span style="background-color: #DCFCE7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">Aktif</span>
                                                @else
                                                    <span style="background-color: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    <a href="{{ route('produk-siap-jual.create', ['produk_paket_id' => $paket->id]) }}" class="btn btn-sm" style="background-color: #DCFCE7; color: #166534; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Lanjutkan ke Produk Siap Jual" onmouseover="this.style.backgroundColor='#BBF7D0'; this.style.boxShadow='0 2px 6px rgba(22, 101, 52, 0.2)';" onmouseout="this.style.backgroundColor='#DCFCE7'; this.style.boxShadow='none';">
                                                        <i class="fas fa-link"></i>
                                                    </a>
                                                    <a href="{{ route('produk-paket.show', $paket) }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0284C7; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Lihat Detail" onmouseover="this.style.backgroundColor='#BFDBFE'; this.style.boxShadow='0 2px 6px rgba(2, 132, 199, 0.2)';" onmouseout="this.style.backgroundColor='#E0F2FE'; this.style.boxShadow='none';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('produk-paket.edit', $paket) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Edit" onmouseover="this.style.backgroundColor='#FDE68A'; this.style.boxShadow='0 2px 6px rgba(146, 64, 14, 0.2)';" onmouseout="this.style.backgroundColor='#FEF3C7'; this.style.boxShadow='none';">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route('produk-paket.destroy', $paket) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" title="Hapus" onclick="return confirm('Yakin ingin menghapus paket ini?');" onmouseover="this.style.backgroundColor='#FCA5A5'; this.style.boxShadow='0 2px 6px rgba(185, 28, 28, 0.2)';" onmouseout="this.style.backgroundColor='#FEE2E2'; this.style.boxShadow='none';">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="d-flex justify-content-center p-4">
                            {{ $pakets->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; background-color: #F9FAFB; border-radius: 12px;">
                            <i class="fas fa-layer-group" style="font-size: 48px; color: #D1D5DB; margin-bottom: 20px; display: block;"></i>
                            <h5 style="color: #6B7280; font-weight: 600; margin-bottom: 10px;">Belum ada produk paket</h5>
                            <p style="color: #9CA3AF; margin-bottom: 24px;">Buat paket/platter untuk mengelompokkan beberapa item dalam satu produk</p>
                            <a href="{{ route('produk-paket.create') }}" class="btn fw-bold" style="background-color: #8B5CF6; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#7C3AED'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.3)';" onmouseout="this.style.backgroundColor='#8B5CF6'; this.style.boxShadow='none';">
                                <i class="fas fa-plus me-2"></i> Buat Paket Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
