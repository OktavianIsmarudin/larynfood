@extends('layouts.guest')

@section('title', 'Pesanan Saya')

@push('styles')
<style>
    .orders-section {
        padding: 80px 0;
        background: #F8F9FA;
        min-height: 100vh;
    }

    .orders-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .order-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .order-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 2px solid #F3F4F6;
        margin-bottom: 15px;
    }

    .order-number {
        font-size: 18px;
        font-weight: 700;
        color: #21489d;
    }

    .order-date {
        color: #6C757D;
        font-size: 14px;
    }

    .status-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #FEF3C7;
        color: #92400E;
    }

    .status-success {
        background: #D1FAE5;
        color: #065F46;
    }

    .status-failed {
        background: #FEE2E2;
        color: #991B1B;
    }

    .status-confirmed {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .order-items {
        margin: 15px 0;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        color: #6C757D;
    }

    .order-total {
        display: flex;
        justify-content: space-between;
        padding-top: 15px;
        border-top: 2px solid #F3F4F6;
        margin-top: 15px;
        font-size: 20px;
        font-weight: 700;
        color: #21489d;
    }

    .order-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-action {
        flex: 1;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-detail {
        background: #21489d;
        color: white;
    }

    .btn-detail:hover {
        background: #1a3a7d;
        color: white;
    }

    .btn-invoice {
        background: white;
        color: #21489d;
        border: 2px solid #21489d;
    }

    .btn-invoice:hover {
        background: #21489d;
        color: white;
    }
</style>
@endpush

@section('content')
<section class="orders-section">
    <div class="container">
        <div class="orders-header">
            <h2><i class="fas fa-shopping-bag"></i> Pesanan Saya</h2>
            <p class="text-muted mb-0">Kelola dan pantau status pesanan Anda</p>

            <form action="{{ route('customer.track.lookup') }}" method="GET" class="row g-2 mt-3">
                <div class="col-md-9">
                    <input type="text" name="order_number" class="form-control" placeholder="Masukkan ID Pesanan (contoh: ORD-20260409-XXX)" required>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary-custom">Lacak Sekarang</button>
                </div>
            </form>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @forelse($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-number">{{ $order->order_number }}</div>
                    <div class="order-date">
                        <i class="far fa-calendar"></i> {{ $order->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
                <div class="text-end">
                    @if($order->payment_status == 'pending')
                        <span class="status-badge status-pending">Menunggu Pembayaran</span>
                    @elseif($order->payment_status == 'success')
                        <span class="status-badge status-success">Dibayar</span>
                    @else
                        <span class="status-badge status-failed">Gagal</span>
                    @endif
                    <br>
                    @if($order->status == 'confirmed')
                        <span class="status-badge status-confirmed mt-2">Dikonfirmasi</span>
                    @endif
                </div>
            </div>

            <div class="order-items">
                <strong>Produk:</strong>
                @foreach($order->items->take(3) as $item)
                <div class="order-item">
                    <span>{{ $item->product_name }} ({{ $item->quantity }}x)</span>
                    <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
                @if($order->items->count() > 3)
                <div class="order-item">
                    <span class="text-muted">+ {{ $order->items->count() - 3 }} produk lainnya</span>
                </div>
                @endif
            </div>

            <div class="order-total">
                <span>Total Pembayaran</span>
                <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="order-actions">
                <a href="{{ route('customer.track', $order->order_number) }}" class="btn-action btn-detail">
                    <i class="fas fa-location-dot"></i> Lacak Pesanan
                </a>
                @if($order->isPaid())
                <a href="{{ route('customer.orders.invoice', $order->order_number) }}" class="btn-action btn-invoice" target="_blank">
                    <i class="fas fa-file-invoice"></i> Cetak Invoice
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="order-card text-center py-5">
            <i class="fas fa-shopping-bag" style="font-size: 80px; color: #D1D5DB; margin-bottom: 20px;"></i>
            <h4>Belum Ada Pesanan</h4>
            <p class="text-muted">Anda belum memiliki pesanan. Mulai belanja sekarang!</p>
            <a href="{{ route('landing') }}" class="btn btn-primary-custom mt-3">
                <i class="fas fa-shopping-cart me-2"></i> Mulai Belanja
            </a>
        </div>
        @endforelse

    </div>
</section>
@endsection
