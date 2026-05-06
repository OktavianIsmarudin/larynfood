@extends('layouts.app')

@section('title', 'Stock Gudang')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-warehouse" style="color: #22C55E; margin-right: 12px; opacity: 0.8;"></i> Stock Gudang
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola inventori produk di gudang</small>
                </div>
                <a href="{{ route('stock-gudang.create') }}" class="btn fw-bold" style="background-color: #22C55E; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#16A34A'; this.style.boxShadow='0 4px 12px rgba(34, 197, 94, 0.3)';" onmouseout="this.style.backgroundColor='#22C55E'; this.style.boxShadow='0 2px 8px rgba(34, 197, 94, 0.2)';">
                    <i class="fas fa-plus me-2"></i> Tambah Stock
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
                    @if ($stocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">SKU</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama Produk</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Kategori</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Stok</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Satuan</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">Harga Modal</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Sumber</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stocks as $stock)
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;"><code style="background-color: #F3F4F6; padding: 4px 8px; border-radius: 4px;">{{ $stock->sku }}</code></td>
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">{{ $stock->nama_produk ?? '-' }}</td>
                                            <td style="padding: 16px 20px; color: #6B7280;">{{ $stock->category->nama_kategori ?? '-' }}</td>
                                            <td style="padding: 16px 20px; color: #6B7280;">
                                                <span style="background-color: #E0F2FE; color: #0369A1; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                    {{ $stock->jumlah_pack ?? $stock->jumlah_stock }} pack + {{ $stock->pcs_sisa ?? $stock->sisa_stock_pcs }} pcs
                                                </span>
                                                <br>
                                                <small style="color: #9CA3AF;">(Total: {{ $stock->total_pcs_attribute }} pcs)</small>
                                            </td>
                                            <td style="padding: 16px 20px; color: #6B7280;">{{ $stock->satuan ?? $stock->satuan_utama }}</td>
                                            <td style="padding: 16px 20px; text-align: right; color: #22C55E; font-weight: 600;">Rp {{ number_format($stock->harga_beli_pack ?? $stock->harga_beli ?? 0, 0, ',', '.') }}</td>
                                            <td style="padding: 16px 20px;">
                                                @if ($stock->purchase_id)
                                                    <span class="badge" style="background-color: #DCFCE7; color: #166534; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Dari Pembelian</span>
                                                @else
                                                    <span class="badge" style="background-color: #F3F4F6; color: #6B7280; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Input Manual</span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('stock-gudang.show', $stock) }}" class="btn btn-sm" style="background-color: #E0F2FE; color: #0284C7; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Lihat Detail" onmouseover="this.style.backgroundColor='#BFDBFE'; this.style.boxShadow='0 2px 6px rgba(2, 132, 199, 0.2)';" onmouseout="this.style.backgroundColor='#E0F2FE'; this.style.boxShadow='none';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if (!$stock->purchase_id)
                                                        <a href="{{ route('stock-gudang.edit', $stock) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Edit" onmouseover="this.style.backgroundColor='#FDE68A'; this.style.boxShadow='0 2px 6px rgba(146, 64, 14, 0.2)';" onmouseout="this.style.backgroundColor='#FEF3C7'; this.style.boxShadow='none';">
                                                            <i class="fas fa-pen"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('stock-gudang.destroy', $stock) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" title="Hapus" onclick="return confirm('Yakin ingin menghapus stock ini?');" onmouseover="this.style.backgroundColor='#FCA5A5'; this.style.boxShadow='0 2px 6px rgba(185, 28, 28, 0.2)';" onmouseout="this.style.backgroundColor='#FEE2E2'; this.style.boxShadow='none';">
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
                            {{ $stocks->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; background-color: #F9FAFB; border-radius: 12px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 20px; display: block;"></i>
                            <h5 style="color: #6B7280; font-weight: 600; margin-bottom: 10px;">Belum ada data stock</h5>
                            <p style="color: #9CA3AF; margin-bottom: 24px;">Tambahkan stock produk untuk mengelola inventori</p>
                            <a href="{{ route('stock-gudang.create') }}" class="btn fw-bold" style="background-color: #22C55E; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#16A34A'; this.style.boxShadow='0 4px 12px rgba(34, 197, 94, 0.3)';" onmouseout="this.style.backgroundColor='#22C55E'; this.style.boxShadow='none';">
                                <i class="fas fa-plus me-2"></i> Tambah Stock Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
