@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Pembelian</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tanggal Pembelian</h6>
                            <p class="fs-5 fw-bold">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Status Stock</h6>
                            <p>
                                @if ($pembelian->status_stock === 'sudah_masuk_gudang')
                                    <span class="badge bg-success">Sudah Masuk Gudang</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Masuk Gudang</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Supplier</h6>
                            <p>{{ $pembelian->supplier->nama_supplier ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Kategori</h6>
                            <p>{{ $pembelian->category->nama_kategori ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-2">Nama Produk</h6>
                            <p class="fs-5 fw-bold">{{ $pembelian->nama_produk }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Jumlah Barang</h6>
                            <p class="fs-5 fw-bold">{{ $pembelian->qty }} unit</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Harga Satuan</h6>
                            <p class="fs-5 fw-bold">Rp {{ number_format($pembelian->total_biaya_awal, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Total Pembelian</h6>
                            <p class="fs-5 fw-bold text-success">Rp {{ number_format($pembelian->total_pengeluaran, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if ($pembelian->bukti_pembelian)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-2">Bukti Pembelian</h6>
                                <p>{{ $pembelian->bukti_pembelian }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($pembelian->status_stock === 'belum_masuk_gudang')
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Status Belum Masuk Gudang</strong>
                            <br>
                            Silakan input stock gudang terlebih dahulu sebelum barang dapat dijual.
                            <br>
                            <a href="{{ route('pembelian.to-stock-gudang', $pembelian) }}" class="btn btn-sm btn-success" style="margin-top: 10px;">
                                <i class="fas fa-check"></i> Masukkan ke Stock Gudang
                            </a>
                        </div>
                    @else
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Status Sudah Masuk Gudang</strong>
                            <br>
                            Stock gudang terkait: 
                            @if ($pembelian->stockGudang)
                                <a href="{{ route('stock-gudang.show', $pembelian->stockGudang) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Lihat Stock Gudang
                                </a>
                            @else
                                <span class="text-muted">Tidak ada data stock gudang</span>
                            @endif
                        </div>
                    @endif

                    <div class="row mt-4 pt-4 border-top">
                        <div class="col-12">
                            <p class="text-muted small">
                                <i class="fas fa-calendar"></i>
                                Dibuat: {{ $pembelian->created_at->format('d M Y H:i') }} |
                                <i class="fas fa-pencil"></i>
                                Diubah: {{ $pembelian->updated_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('pembelian.edit', $pembelian) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        <form action="{{ route('pembelian.destroy', $pembelian) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>

                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
