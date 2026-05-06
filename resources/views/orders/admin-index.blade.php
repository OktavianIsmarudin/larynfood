@extends('layouts.app')

@section('title', 'Tracking Pesanan')
@section('page-title', 'Tracking Pesanan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Pesanan Pelanggan</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari ID pesanan / nama / no telp">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="payment_status">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="success" {{ request('payment_status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Tracking</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pesanan Dibuat</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Verifikasi Bayar</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Siap Diambil/Dikirim</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Saring</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>No Telp</th>
                        <th>Pengiriman</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Tracking</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                        $trackingStatusLabel = match($order->status) {
                            'pending' => 'Pesanan Dibuat',
                            'confirmed' => 'Verifikasi Bayar',
                            'processing' => 'Sedang Diproses',
                            'shipped' => ((float) $order->shipping_cost <= 0 ? 'Siap Diambil' : 'Dalam Perjalanan'),
                            'delivered' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => strtoupper($order->status),
                        };
                        $paymentStatusLabel = match($order->payment_status) {
                            'pending' => 'Menunggu',
                            'success' => 'Berhasil',
                            'failed' => 'Gagal',
                            default => strtoupper($order->payment_status),
                        };
                    @endphp
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>{{ (float) $order->shipping_cost <= 0 ? 'Ambil' : 'Dikirim' }}</td>
                        <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'success' ? 'bg-success' : ($order->payment_status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ $paymentStatusLabel }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $trackingStatusLabel }}</span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('orders-management.show', $order->id) }}" class="btn btn-sm btn-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>
</div>
@endsection
