@extends('layouts.guest')

@section('title', 'Detail Pesanan')

@push('styles')
<style>
    .order-detail-section {
        padding: 80px 0;
        background: #F8F9FA;
        min-height: 100vh;
    }

    .detail-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .order-status-tracker {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 30px 0;
        position: relative;
    }

    .status-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .status-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #E5E7EB;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #9CA3AF;
    }

    .status-step.active .status-circle {
        background: #21489d;
        color: white;
    }

    .status-step.completed .status-circle {
        background: #10B981;
        color: white;
    }

    .status-line {
        position: absolute;
        top: 30px;
        left: 0;
        right: 0;
        height: 2px;
        background: #E5E7EB;
        z-index: 1;
    }

    .product-list {
        border: 2px solid #F3F4F6;
        border-radius: 8px;
        padding: 20px;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .info-row:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<section class="order-detail-section">
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-custom">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pesanan
            </a>
        </div>

        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>Detail Pesanan</h3>
                    <p class="text-muted mb-0">{{ $order->order_number }}</p>
                </div>
                <div class="text-end">
                    @if($order->payment_status == 'pending')
                        <span class="badge bg-warning text-dark" style="font-size: 14px; padding: 8px 15px;">Menunggu Pembayaran</span>
                    @elseif($order->payment_status == 'success')
                        <span class="badge bg-success" style="font-size: 14px; padding: 8px 15px;">Dibayar</span>
                    @else
                        <span class="badge bg-danger" style="font-size: 14px; padding: 8px 15px;">Gagal</span>
                    @endif
                </div>
            </div>

            @if($order->isPaid())
            <div class="order-status-tracker">
                <div class="status-line"></div>
                <div class="status-step completed">
                    <div class="status-circle"><i class="fas fa-check"></i></div>
                    <small><strong>Dibayar</strong></small><br>
                    <small class="text-muted">{{ $order->paid_at?->format('d M Y') }}</small>
                </div>
                <div class="status-step {{ $order->confirmed_at ? 'completed' : ($order->isPaid() ? 'active' : '') }}">
                    <div class="status-circle"><i class="fas fa-box-check"></i></div>
                    <small><strong>Dikonfirmasi</strong></small><br>
                    <small class="text-muted">{{ $order->confirmed_at?->format('d M Y') ?? '-' }}</small>
                </div>
                <div class="status-step {{ $order->shipped_at ? 'completed' : '' }}">
                    <div class="status-circle"><i class="fas fa-truck"></i></div>
                    <small><strong>Dikirim</strong></small><br>
                    <small class="text-muted">{{ $order->shipped_at?->format('d M Y') ?? '-' }}</small>
                </div>
                <div class="status-step {{ $order->delivered_at ? 'completed' : '' }}">
                    <div class="status-circle"><i class="fas fa-home"></i></div>
                    <small><strong>Diterima</strong></small><br>
                    <small class="text-muted">{{ $order->delivered_at?->format('d M Y') ?? '-' }}</small>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Silakan selesaikan pembayaran untuk melanjutkan pesanan Anda.
            </div>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="detail-card">
                    <h4 class="mb-4"><i class="fas fa-shopping-bag"></i> Produk yang Dipesan</h4>
                    <div class="product-list">
                        @foreach($order->items as $item)
                        <div class="product-item">
                            <div>
                                <strong>{{ $item->product_name }}</strong><br>
                                <small class="text-muted">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</small>
                            </div>
                            <div class="text-end">
                                <strong style="color: #21489d;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4">
                            <div class="d-flex justify-content-between py-2">
                                <span>Subtotal</span>
                                <strong>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span>Ongkos Kirim</span>
                                <strong>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-3 border-top" style="font-size: 20px; font-weight: 700; color: #21489d;">
                                <span>Total</span>
                                <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="detail-card">
                    <h5 class="mb-3"><i class="fas fa-user"></i> Informasi Pembeli</h5>
                    <div class="info-row">
                        <span class="text-muted">Nama</span>
                        <strong>{{ $order->customer_name }}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Email</span>
                        <strong>{{ $order->customer_email }}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Telepon</span>
                        <strong>{{ $order->customer_phone }}</strong>
                    </div>
                </div>

                <div class="detail-card">
                    <h5 class="mb-3"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h5>
                    <p>{{ $order->shipping_address }}</p>
                    @if($order->notes)
                    <div class="alert alert-info mt-3">
                        <strong>Catatan:</strong><br>
                        {{ $order->notes }}
                    </div>
                    @endif
                </div>

                @if($order->isPaid())
                <a href="{{ route('customer.orders.invoice', $order->order_number) }}" class="btn btn-primary-custom w-100" target="_blank">
                    <i class="fas fa-file-invoice me-2"></i> Cetak Invoice
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
