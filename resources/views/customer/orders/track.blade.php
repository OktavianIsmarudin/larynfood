@extends('layouts.guest')

@section('title', 'Track Pesanan')

@push('styles')
<style>
    .track-section {
        padding: 80px 0;
        background: #F8F9FA;
        min-height: 100vh;
    }

    .track-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 30px;
    }

    .order-code {
        color: #21489d;
        font-weight: 700;
    }

    .status-box {
        border: 2px solid #21489d;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        color: #21489d;
        font-weight: 700;
        margin: 20px 0 30px;
        background: #F7FAFF;
    }

    .timeline {
        position: relative;
        margin: 10px 0 30px;
    }

    .timeline-item {
        display: flex;
        gap: 14px;
        margin-bottom: 26px;
        position: relative;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #D1D5DB;
        color: white;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        margin-top: 2px;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 18px;
        top: 44px;
        width: 2px;
        height: calc(100% + 4px);
        background: #D1D5DB;
    }

    .timeline-item:last-child::after {
        display: none;
    }

    .timeline-item.active .timeline-dot,
    .timeline-item.completed .timeline-dot {
        background: #21489d;
    }

    .timeline-item.completed .timeline-dot {
        background: #10B981;
    }

    .timeline-item.completed::after {
        background: #10B981;
    }

    .timeline-title {
        font-weight: 700;
        margin-bottom: 3px;
    }

    .timeline-note {
        color: #6C757D;
        margin: 0;
    }

    .timeline-state {
        color: #F59E0B;
        font-weight: 700;
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
@php
    $isPickup = (float) $order->shipping_cost <= 0;
    $deliveryLabel = $isPickup ? 'Siap Diambil' : 'Dalam Perjalanan';

    $steps = [
        [
            'key' => 'created',
            'title' => 'Pesanan Dibuat',
            'note' => 'Pesanan berhasil diterima',
            'done' => true,
            'active' => $order->status === 'pending',
        ],
        [
            'key' => 'payment_verification',
            'title' => 'Verifikasi Bayar',
            'note' => 'Kasir sedang memverifikasi pembayaran',
            'done' => in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true) || $order->payment_status === 'success',
            'active' => $order->status === 'pending' || $order->status === 'confirmed',
        ],
        [
            'key' => 'processing',
            'title' => 'Sedang Diproses',
            'note' => 'Pesanan sedang disiapkan',
            'done' => in_array($order->status, ['shipped', 'delivered'], true),
            'active' => $order->status === 'processing',
        ],
        [
            'key' => 'ready',
            'title' => $deliveryLabel,
            'note' => $isPickup ? 'Silakan ambil pesananmu' : 'Pesanan sedang dalam perjalanan ke alamatmu',
            'done' => $order->status === 'delivered',
            'active' => $order->status === 'shipped',
        ],
        [
            'key' => 'done',
            'title' => 'Selesai',
            'note' => 'Selamat menikmati!',
            'done' => $order->status === 'delivered',
            'active' => false,
        ],
    ];

    $statusLabel = match ($order->status) {
        'pending' => 'Menunggu verifikasi kasir...',
        'confirmed' => 'Pembayaran terverifikasi',
        'processing' => 'Pesanan sedang diproses',
        'shipped' => $isPickup ? 'Pesanan siap diambil' : 'Pesanan dalam perjalanan',
        'delivered' => 'Pesanan selesai',
        'cancelled' => 'Pesanan dibatalkan',
        default => 'Status diperbarui',
    };
@endphp

<section class="track-section">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="track-card">
                    <h2 class="text-center fw-bold mb-2">Lacak Pesanan</h2>
                    <p class="text-center mb-0">Pesanan: <span class="order-code">{{ $order->order_number }}</span></p>

                    <div class="status-box">{{ $statusLabel }}</div>

                    <div class="timeline">
                        @foreach($steps as $step)
                        <div class="timeline-item {{ $step['done'] ? 'completed' : ($step['active'] ? 'active' : '') }}">
                            <div class="timeline-dot">
                                @if($step['done'])
                                <i class="fas fa-check"></i>
                                @else
                                <i class="fas fa-circle" style="font-size: 8px;"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="timeline-title">{{ $step['title'] }}</h5>
                                <p class="timeline-note">{{ $step['note'] }}</p>
                                @if($step['active'])
                                <p class="timeline-state">Sedang berlangsung...</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($order->tracking_note)
                    <div class="alert alert-info">
                        <strong>Keterangan Admin:</strong><br>
                        {{ $order->tracking_note }}
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('products.explore') }}" class="btn btn-primary-custom btn-lg">Pesan Lagi</a>
                        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-custom">Lihat Daftar Tracking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
setTimeout(function () {
    window.location.reload();
}, 5000);
</script>
@endpush
