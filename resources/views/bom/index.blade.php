@extends('layouts.app')

@section('title', 'Bill of Material (BOM)')

@section('content')
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                        <i class="fas fa-file-recipe" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Bill of Material (BOM)
                    </h2>
                    <small style="color: #6B7280; font-size: 14px;">Kelola daftar resep/komposisi paket produk Anda</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('bom.export-excel') }}" class="btn fw-bold" style="background-color: #10B981; color: white; padding: 12px 20px; font-size: 14px; border-radius: 8px; border: none; transition: all 0.3s ease; text-decoration: none;" title="Export semua BOM ke Excel">
                        <i class="fas fa-download me-2"></i> Export Excel
                    </a>
                    <a href="{{ route('produk-paket.create') }}" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2); border: none; transition: all 0.3s ease; text-decoration: none;" onmouseover="this.style.backgroundColor='#D97706'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)';" onmouseout="this.style.backgroundColor='#F59E0B'; this.style.boxShadow='0 2px 8px rgba(245, 158, 11, 0.2)';">
                        <i class="fas fa-plus me-2"></i> Buat BOM Baru
                    </a>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #F59E0B 0%, #F97316 100%); color: white; overflow: hidden;">
                        <div class="card-body" style="padding: 24px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small style="opacity: 0.9; font-size: 12px; font-weight: 500;">Total BOM</small>
                                    <h3 style="font-size: 28px; font-weight: 700; margin: 8px 0 0 0;">{{ $totalBom ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-layer-group" style="font-size: 32px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; overflow: hidden;">
                        <div class="card-body" style="padding: 24px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small style="opacity: 0.9; font-size: 12px; font-weight: 500;">BOM Aktif</small>
                                    <h3 style="font-size: 28px; font-weight: 700; margin: 8px 0 0 0;">{{ $bomAktif ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-check-circle" style="font-size: 32px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; overflow: hidden;">
                        <div class="card-body" style="padding: 24px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small style="opacity: 0.9; font-size: 12px; font-weight: 500;">Total Item</small>
                                    <h3 style="font-size: 28px; font-weight: 700; margin: 8px 0 0 0;">{{ $totalItem ?? 0 }}</h3>
                                </div>
                                <i class="fas fa-list" style="font-size: 32px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background: linear-gradient(135deg, #EC4899 0%, #DB2777 100%); color: white; overflow: hidden;">
                        <div class="card-body" style="padding: 24px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small style="opacity: 0.9; font-size: 12px; font-weight: 500;">Total HPP</small>
                                    <h3 style="font-size: 20px; font-weight: 700; margin: 8px 0 0 0;">{{ $totalHpp ?? 'Rp 0' }}</h3>
                                </div>
                                <i class="fas fa-money-bill-wave" style="font-size: 32px; opacity: 0.3;"></i>
                            </div>
                        </div>
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

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; border: 1px solid #FEE2E2; background-color: #FEF2F2;">
                    <i class="fas fa-exclamation-circle" style="color: #DC3545; margin-right: 8px;"></i> <strong>Error!</strong> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- SEARCH AND FILTER --}}
            <div class="card border-0 mb-4" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body" style="padding: 20px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari BOM, kode, atau deskripsi..." style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">
                        </div>
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-select" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #E5E7EB;">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn w-100" id="btnReset" style="background-color: #E5E7EB; color: #374151; border: none; border-radius: 8px; padding: 12px 16px; font-weight: 500;">
                                <i class="fas fa-redo me-2"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOM TABLE --}}
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); background-color: #FFFFFF;">
                <div class="card-body p-0">
                    @if ($pakets && $pakets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="bomTable" style="font-size: 14px;">
                                <thead>
                                    <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Nama BOM</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px;">Kode</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Item</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: right;">HPP Total</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Produk Siap Jual</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Status</th>
                                        <th style="color: #6B7280; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pakets as $bom)
                                        <tr style="border-bottom: 1px solid #E5E7EB; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#F9FAFB';" onmouseout="this.style.backgroundColor='white';">
                                            <td style="padding: 16px 20px; color: #1A1A1A; font-weight: 500;">
                                                <div>{{ $bom->nama_paket }}</div>
                                                @if ($bom->deskripsi)
                                                    <small style="color: #6B7280;">{{ substr($bom->deskripsi, 0, 40) }}{{ strlen($bom->deskripsi) > 40 ? '...' : '' }}</small>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; color: #6B7280;">
                                                <code style="background-color: #F3F4F6; padding: 4px 8px; border-radius: 4px;">{{ $bom->kode_paket ?? '-' }}</code>
                                            </td>
                                            <td style="padding: 16px 20px; color: #1A1A1A; text-align: center; font-weight: 600;">
                                                <span style="background-color: #E0E7FF; color: #4F46E5; padding: 4px 12px; border-radius: 16px; font-size: 12px;">{{ $bom->details_count ?? 0 }} item</span>
                                            </td>
                                            <td style="padding: 16px 20px; color: #166534; text-align: right; font-weight: 600;">
                                                Rp {{ number_format($bom->hpp_total ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <span style="background-color: #FEF3C7; color: #92400E; padding: 4px 12px; border-radius: 16px; font-size: 12px;">{{ $bom->produk_siap_juals_count ?? 0 }}</span>
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                @if ($bom->status == 'aktif')
                                                    <span style="background-color: #D1FAE5; color: #065F46; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                        <i class="fas fa-check-circle me-1"></i> Aktif
                                                    </span>
                                                @else
                                                    <span style="background-color: #FEE2E2; color: #991B1B; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                                        <i class="fas fa-times-circle me-1"></i> Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 20px; text-align: center;">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('bom.details', $bom->id) }}" class="btn btn-outline-primary" title="Lihat Detail" style="border-color: #3B82F6; color: #3B82F6; text-decoration: none; padding: 6px 10px; border-radius: 4px; transition: all 0.3s ease; font-size: 12px;" onmouseover="this.style.backgroundColor='#3B82F6'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#3B82F6';">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('produk-paket.edit', $bom->id) }}" class="btn btn-outline-warning" title="Edit" style="border-color: #F59E0B; color: #F59E0B; text-decoration: none; padding: 6px 10px; border-radius: 4px; transition: all 0.3s ease; font-size: 12px; margin: 0 4px;" onmouseover="this.style.backgroundColor='#F59E0B'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#F59E0B';">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('produk-paket.destroy', $bom->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus BOM ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Hapus" style="border-color: #DC3545; color: #DC3545; text-decoration: none; padding: 6px 10px; border-radius: 4px; transition: all 0.3s ease; font-size: 12px; border: 1px solid #DC3545;" onmouseover="this.style.backgroundColor='#DC3545'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#DC3545';">
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
                        <div style="padding: 20px; border-top: 1px solid #E5E7EB;">
                            {{ $pakets->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div style="padding: 60px 20px; text-align: center;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #D1D5DB; margin-bottom: 16px; display: block;"></i>
                            <p style="color: #6B7280; font-size: 16px; margin-bottom: 8px;">Belum ada BOM yang dibuat</p>
                            <p style="color: #9CA3AF; font-size: 14px; margin-bottom: 24px;">Mulai buat BOM pertama Anda dengan klik tombol "Buat BOM Baru"</p>
                            <a href="{{ route('produk-paket.create') }}" class="btn" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none;">
                                <i class="fas fa-plus me-2"></i> Buat BOM Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .btn-group .btn {
            width: 100%;
            margin-bottom: 4px;
        }
    }
</style>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#bomTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Filter by status
    document.getElementById('filterStatus').addEventListener('change', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#bomTable tbody tr');
        
        rows.forEach(row => {
            if (!filter) {
                row.style.display = '';
            } else {
                const statusCell = row.querySelector('td:nth-child(6)');
                const statusText = statusCell.textContent.toLowerCase();
                row.style.display = statusText.includes(filter) ? '' : 'none';
            }
        });
    });

    // Reset filters
    document.getElementById('btnReset').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterStatus').value = '';
        document.querySelectorAll('#bomTable tbody tr').forEach(row => {
            row.style.display = '';
        });
    });
</script>
@endsection
