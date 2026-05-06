@extends('layouts.app')

@section('title', 'Saldo Modal / Kas Usaha')

@section('content')
<style>
    .saldo-hero-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.15s ease;
    }
    .saldo-hero-card:hover { transform: translateY(-2px); }
    .saldo-hero-card .card-body { padding: 1.25rem 1.5rem; }
    .saldo-hero-card .saldo-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .saldo-hero-card .saldo-label { font-size: 0.78rem; color: #6c757d; letter-spacing: 0.02em; }
    .saldo-hero-card .saldo-value { font-size: 1.15rem; font-weight: 700; margin: 0; }

    .saldo-akhir-card {
        border: 2px solid transparent;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    .saldo-akhir-card.positive { border-color: #d1fae5; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); }
    .saldo-akhir-card.negative { border-color: #fecaca; background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%); }
    .saldo-akhir-card .saldo-value { font-size: 1.35rem; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;
    }
    .status-pill.aman { background: #d1fae5; color: #065f46; }
    .status-pill.minus { background: #fecaca; color: #991b1b; }

    .info-row-card {
        border: none; border-radius: 12px; transition: box-shadow 0.15s ease;
    }
    .info-row-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important; }
    .info-row-card .info-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .section-title {
        font-size: 0.82rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #6c757d; margin-bottom: 1rem;
    }

    .cta-group .btn { border-radius: 10px; padding: 0.55rem 1.2rem; font-weight: 600; font-size: 0.85rem; }

    .data-card { border: none; border-radius: 14px; overflow: hidden; }
    .data-card .card-header {
        border: none; padding: 0.9rem 1.25rem;
        font-weight: 700; font-size: 0.88rem; letter-spacing: 0.01em;
    }
    .data-card .table th {
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;
        color: #6c757d; font-weight: 600; padding: 0.7rem 1rem; border-bottom: 2px solid #e9ecef;
    }
    .data-card .table td { padding: 0.7rem 1rem; vertical-align: middle; font-size: 0.87rem; }
    .data-card .table tbody tr:last-child td { border-bottom: none; }

    .empty-state {
        padding: 2.5rem 1rem; text-align: center;
    }
    .empty-state .empty-icon {
        width: 64px; height: 64px; border-radius: 16px;
        display: inline-flex; align-items: center; justify-content: center;
        margin-bottom: 1rem;
    }
    .empty-state p { color: #9ca3af; font-size: 0.9rem; margin-bottom: 1rem; }
</style>

<div class="container-fluid">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius:12px;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ====================== PAGE HEADER ====================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-wallet me-2" style="color:#6366f1;"></i>Saldo Modal / Kas Usaha
            </h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Kelola modal dan pantau arus kas usaha Anda</p>
        </div>
    </div>

    {{-- ====================== SUMMARY CARDS ====================== --}}
    <div class="row g-3 mb-4">
        {{-- Saldo Awal --}}
        <div class="col-6 col-lg-3">
            <div class="card saldo-hero-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="saldo-icon" style="background:#ede9fe;">
                            <i class="fas fa-piggy-bank" style="color:#7c3aed;"></i>
                        </span>
                    </div>
                    <span class="saldo-label d-block">Total Saldo Awal</span>
                    <p class="saldo-value" style="color:#7c3aed;">Rp {{ number_format($overview['total_saldo_awal'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Total Penggunaan --}}
        <div class="col-6 col-lg-3">
            <div class="card saldo-hero-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="saldo-icon" style="background:#fee2e2;">
                            <i class="fas fa-arrow-circle-down" style="color:#dc2626;"></i>
                        </span>
                    </div>
                    <span class="saldo-label d-block">Total Pemakaian</span>
                    <p class="saldo-value text-danger">Rp {{ number_format($overview['total_penggunaan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Pemasukan Kembali --}}
        <div class="col-6 col-lg-3">
            <div class="card saldo-hero-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="saldo-icon" style="background:#d1fae5;">
                            <i class="fas fa-arrow-circle-up" style="color:#059669;"></i>
                        </span>
                    </div>
                    <span class="saldo-label d-block">Pemasukan Kembali</span>
                    <p class="saldo-value text-success">Rp {{ number_format($overview['total_pemasukan_kembali'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Saldo Akhir (Highlighted) --}}
        <div class="col-6 col-lg-3">
            <div class="card saldo-akhir-card shadow-sm h-100 {{ $overview['is_negative'] ? 'negative' : 'positive' }}">
                <div class="card-body" style="padding:1.25rem 1.5rem;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="saldo-icon" style="background:{{ $overview['is_negative'] ? '#fecaca' : '#a7f3d0' }};">
                            <i class="fas fa-balance-scale" style="color:{{ $overview['is_negative'] ? '#dc2626' : '#059669' }};"></i>
                        </span>
                        @if($overview['is_negative'])
                            <span class="status-pill minus"><i class="fas fa-exclamation-circle"></i> Saldo Minus</span>
                        @else
                            <span class="status-pill aman"><i class="fas fa-check-circle"></i> Aman</span>
                        @endif
                    </div>
                    <span class="saldo-label d-block">Saldo Akhir</span>
                    <p class="saldo-value {{ $overview['is_negative'] ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($overview['saldo_akhir'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================== CTA BUTTONS ====================== --}}
    <div class="cta-group d-flex gap-2 flex-wrap mb-4">
        <a href="{{ route('saldo-modal.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Saldo Modal
        </a>
        <a href="{{ route('saldo-modal.penggunaan.create') }}" class="btn btn-warning text-dark shadow-sm">
            <i class="fas fa-minus-circle me-1"></i> Catat Penggunaan Modal
        </a>
    </div>

    {{-- ====================== DATA DETAIL ====================== --}}
    <div class="row g-4">
        {{-- Daftar Saldo Modal --}}
        <div class="col-lg-6">
            <div class="card data-card shadow-sm">
                <div class="card-header text-white d-flex align-items-center" style="background:linear-gradient(135deg,#6366f1,#818cf8);">
                    <i class="fas fa-piggy-bank me-2"></i> Daftar Saldo Modal
                    @if($saldoModals->total() > 0)
                        <span class="badge bg-white bg-opacity-25 ms-auto" style="font-size:0.7rem;">
                            {{ $saldoModals->total() }} data
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($saldoModals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Sumber</th>
                                        <th class="text-end">Saldo Awal</th>
                                        <th class="text-end">Sisa Saldo</th>
                                        <th style="width:48px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($saldoModals as $sm)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold" style="font-size:0.85rem;">{{ $sm->tanggal->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                <span style="font-size:0.85rem;">{{ $sm->sumber_modal ?? '-' }}</span>
                                                @if($sm->keterangan)
                                                    <br><small class="text-muted" style="font-size:0.75rem;">{{ Str::limit($sm->keterangan, 30) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold" style="font-size:0.88rem;">
                                                Rp {{ number_format($sm->saldo_awal, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-bold {{ $sm->saldo_akhir < 0 ? 'text-danger' : 'text-success' }}" style="font-size:0.88rem;">
                                                Rp {{ number_format($sm->saldo_akhir, 0, ',', '.') }}
                                                @if($sm->saldo_akhir < 0)
                                                    <br><span class="status-pill minus" style="font-size:0.6rem;">DEFISIT</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('saldo-modal.destroy', $sm) }}" method="POST"
                                                      onsubmit="return confirm('Hapus saldo modal ini? Semua penggunaan terkait juga akan dihapus.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus" style="border-radius:8px;">
                                                        <i class="fas fa-trash-alt" style="font-size:0.75rem;"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3 py-2 border-top">
                            {{ $saldoModals->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon" style="background:#ede9fe;">
                                <i class="fas fa-piggy-bank fa-lg" style="color:#7c3aed;"></i>
                            </div>
                            <p>Belum ada data saldo modal.<br>Mulai dengan mencatat modal usaha Anda.</p>
                            <a href="{{ route('saldo-modal.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
                                <i class="fas fa-plus me-1"></i> Tambah Saldo Modal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat Penggunaan Modal --}}
        <div class="col-lg-6">
            <div class="card data-card shadow-sm">
                <div class="card-header text-dark d-flex align-items-center" style="background:linear-gradient(135deg,#fbbf24,#f59e0b);">
                    <i class="fas fa-exchange-alt me-2"></i> Riwayat Penggunaan Modal
                    @if($penggunaans->total() > 0)
                        <span class="badge bg-dark bg-opacity-25 ms-auto text-white" style="font-size:0.7rem;">
                            {{ $penggunaans->total() }} data
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($penggunaans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Nominal</th>
                                        <th style="width:48px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penggunaans as $p)
                                        <tr>
                                            <td>
                                                <span style="font-size:0.85rem;">{{ $p->created_at->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                @if($p->jenis === 'pengeluaran')
                                                    <span class="badge text-white" style="background:#ef4444;border-radius:6px;font-size:0.7rem;">
                                                        <i class="fas fa-arrow-down me-1" style="font-size:0.6rem;"></i>Keluar
                                                    </span>
                                                @else
                                                    <span class="badge text-white" style="background:#22c55e;border-radius:6px;font-size:0.7rem;">
                                                        <i class="fas fa-arrow-up me-1" style="font-size:0.6rem;"></i>Masuk
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small style="font-size:0.82rem;">
                                                    {{ $p->keterangan ?? '-' }}
                                                    @if($p->pembelian)
                                                        <br><span style="color:#3b82f6;font-size:0.78rem;"><i class="fas fa-cube me-1"></i>{{ $p->pembelian->nama_produk }}</span>
                                                    @endif
                                                    @if($p->penjualan)
                                                        <br><span style="color:#22c55e;font-size:0.78rem;"><i class="fas fa-receipt me-1"></i>Penjualan #{{ $p->penjualan->id }}</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td class="text-end fw-bold {{ $p->jenis === 'pengeluaran' ? 'text-danger' : 'text-success' }}" style="font-size:0.88rem;">
                                                {{ $p->jenis === 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($p->nominal, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <form action="{{ route('saldo-modal.penggunaan.destroy', $p) }}" method="POST"
                                                      onsubmit="return confirm('Hapus penggunaan modal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus" style="border-radius:8px;">
                                                        <i class="fas fa-trash-alt" style="font-size:0.75rem;"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3 py-2 border-top">
                            {{ $penggunaans->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon" style="background:#fef3c7;">
                                <i class="fas fa-exchange-alt fa-lg" style="color:#d97706;"></i>
                            </div>
                            <p>Belum ada riwayat penggunaan modal.<br>Catat penggunaan untuk melacak arus modal.</p>
                            <a href="{{ route('saldo-modal.penggunaan.create') }}" class="btn btn-warning btn-sm text-dark" style="border-radius:8px;">
                                <i class="fas fa-plus me-1"></i> Catat Penggunaan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
