@extends('layouts.app')

@section('title', 'Data Penjualan')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-shopping-cart" style="color: #06B6D4; margin-right: 12px; opacity: 0.8;"></i> Data Penjualan
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola transaksi penjualan kepada customer</small>
                </div>
                <a href="{{ route('penjualan.create') }}" class="btn fw-bold" style="background-color: #06B6D4; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(6, 182, 212, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#0891B2'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.3)';" onmouseout="this.style.backgroundColor='#06B6D4'; this.style.boxShadow='0 2px 8px rgba(6, 182, 212, 0.2)';">
                    <i class="fas fa-plus me-2"></i> Tambah Penjualan
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
                    @if ($penjualan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Tanggal</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Customer</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Produk</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Qty</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Harga Satuan</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Total Bayar</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Status</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penjualan as $p)
                                        @php
                                            $autoOrderNumber = null;
                                            if (!empty($p->keterangan) && preg_match('/\[AUTO-ORDER:([^\]]+)\]/', $p->keterangan, $matches)) {
                                                $autoOrderNumber = $matches[1];
                                            }
                                        @endphp
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">{{ \Carbon\Carbon::parse($p->tanggal_penjualan)->format('d M Y') }}</td>
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                                {{ $p->customer->nama_customer ?? '-' }}
                                                @if($autoOrderNumber)
                                                    <div class="mt-1">
                                                        <span class="badge" style="background-color: #E0F2FE; color: #075985; font-size: 11px; padding: 4px 8px; border-radius: 999px; font-weight: 600;">Auto Order</span>
                                                        <small style="color: #64748B; font-weight: 600; margin-left: 6px;">{{ $autoOrderNumber }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; color: #6B7280;">
                                                @if($p->produk && $p->produk->isPaket())
                                                    <span style="background-color: #E8E8FF; color: #5B21B6; padding: 1px 5px; border-radius: 4px; font-size: 10px; font-weight: 600; margin-right: 4px;">PAKET</span>
                                                @endif
                                                {{ $p->nama_produk_display }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: right; color: #1A1A1A; font-weight: 500;">{{ $p->jumlah_pcs }} pcs</td>
                                            <td style="padding: 16px 20px; text-align: right; color: #6B7280;">Rp {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px; text-align: right; color: #06B6D4; font-weight: 600;">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px;">
                                                @if ($p->status_pembayaran == 'lunas')
                                                    <span class="badge" style="background-color: #DCFCE7; color: #166534; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Lunas</span>
                                                @elseif ($p->status_pembayaran == 'dp')
                                                    <span class="badge" style="background-color: #FEF3C7; color: #92400E; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">DP</span>
                                                @else
                                                    <span class="badge" style="background-color: #FEE2E2; color: #B91C1C; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Utang</span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('penjualan.show', $p) }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0369A1; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Detail" onmouseover="this.style.backgroundColor='#BAE6FD'; this.style.boxShadow='0 2px 6px rgba(3, 105, 161, 0.2)';" onmouseout="this.style.backgroundColor='#E0F2FE'; this.style.boxShadow='none';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('penjualan.edit', $p) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Edit" onmouseover="this.style.backgroundColor='#FDE68A'; this.style.boxShadow='0 2px 6px rgba(146, 64, 14, 0.2)';" onmouseout="this.style.backgroundColor='#FEF3C7'; this.style.boxShadow='none';">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route('penjualan.destroy', $p) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" title="Hapus" onclick="return confirm('Yakin ingin menghapus?');" onmouseover="this.style.backgroundColor='#FCA5A5'; this.style.boxShadow='0 2px 6px rgba(185, 28, 28, 0.2)';" onmouseout="this.style.backgroundColor='#FEE2E2'; this.style.boxShadow='none';">
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
                            {{ $penjualan->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; background-color: #F9FAFB; border-radius: 12px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 20px; display: block;"></i>
                            <h5 style="color: #6B7280; font-weight: 600; margin-bottom: 10px;">Belum ada data penjualan</h5>
                            <p style="color: #9CA3AF; margin-bottom: 24px;">Tambahkan penjualan baru untuk mencatat transaksi dengan customer</p>
                            <a href="{{ route('penjualan.create') }}" class="btn fw-bold" style="background-color: #06B6D4; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#0891B2'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.3)';" onmouseout="this.style.backgroundColor='#06B6D4'; this.style.boxShadow='none';">
                                <i class="fas fa-plus me-2"></i> Tambah Penjualan Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
