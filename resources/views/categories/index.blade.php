@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-tags" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Daftar Kategori Produk
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola kategori produk dan bahan baku</small>
                </div>
                <a href="{{ route('categories.create') }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#D97706'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)';" onmouseout="this.style.backgroundColor='#F59E0B'; this.style.boxShadow='0 2px 8px rgba(245, 158, 11, 0.2)';">
                    <i class="fas fa-plus me-2"></i> Tambah Kategori
                </a>
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
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; width: 60px;">No</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama Kategori</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Jenis</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Deskripsi</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $index => $category)
                                    <tr style="border-bottom: 1px solid #E5E7EB; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='transparent';">
                                        <td style="padding: 16px 20px; color: #6B7280; font-weight: 500;">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                        <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">{{ $category->nama_kategori }}</td>
                                        <td style="padding: 16px 20px;">
                                            @if($category->jenis_kategori === 'produk')
                                                <span class="badge" style="background-color: #DCFCE7; color: #166534; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Bahan Baku</span>
                                            @else
                                                <span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 500;">Peralatan / Kemasan</span>
                                            @endif
                                        </td>
                                        <td style="padding: 16px 20px; color: #6B7280;">{{ Str::limit($category->deskripsi, 50) ?? '-' }}</td>
                                        <td style="padding: 16px 20px; text-align: center;">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm" style="background-color: #FEF3C7; color: #92400E; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease;" title="Edit" onmouseover="this.style.backgroundColor='#FDE68A'; this.style.boxShadow='0 2px 6px rgba(146, 64, 14, 0.2)';" onmouseout="this.style.backgroundColor='#FEF3C7'; this.style.boxShadow='none';">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm" style="background-color: #FEE2E2; color: #B91C1C; border: none; border-radius: 6px; padding: 6px 10px; font-size: 12px; transition: all 0.2s ease; cursor: pointer;" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" onmouseover="this.style.backgroundColor='#FCA5A5'; this.style.boxShadow='0 2px 6px rgba(185, 28, 28, 0.2)';" onmouseout="this.style.backgroundColor='#FEE2E2'; this.style.boxShadow='none';">
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
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; background-color: #F9FAFB; border-radius: 12px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 20px; display: block;"></i>
                            <h5 style="color: #6B7280; font-weight: 600; margin-bottom: 10px;">Belum ada data kategori</h5>
                            <p style="color: #9CA3AF; margin-bottom: 24px;">Tambahkan kategori untuk mengorganisir produk</p>
                            <a href="{{ route('categories.create') }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 10px 20px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#D97706'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)';" onmouseout="this.style.backgroundColor='#F59E0B'; this.style.boxShadow='none';">
                                <i class="fas fa-plus me-2"></i> Tambah Kategori Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
