@extends('layouts.app')

@section('title', 'Detail Tracking Pesanan')
@section('page-title', 'Detail Tracking Pesanan')

@section('content')
<div class="mb-3">
    <a href="{{ route('orders-management.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-3">
    @php
        $headerStatusLabel = match($order->status) {
            'delivered' => 'Selesai',
            default => strtoupper($order->status),
        };
        $paymentStatusLabel = match($order->payment_status) {
            'pending' => 'Menunggu',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            default => strtoupper($order->payment_status),
        };
    @endphp
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $order->order_number }}</h5>
                <span class="badge bg-info text-dark">{{ $headerStatusLabel }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal</th>
                                <th>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Ongkir</th>
                                <th>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Nama:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email ?: '-' }}</p>
                <p class="mb-1"><strong>Telepon:</strong> {{ $order->customer_phone }}</p>
                <p class="mb-1"><strong>Pengiriman:</strong> {{ (float) $order->shipping_cost <= 0 ? 'Ambil di tempat' : 'Dikirim' }}</p>
                <p class="mb-0"><strong>Alamat:</strong> {{ $order->shipping_address }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Bukti Pembayaran</h6>
            </div>
            <div class="card-body">
                @if($order->payment_proof_path)
                <a href="{{ route('orders-management.payment-proof', $order->id) }}" target="_blank">
                    <img src="{{ route('orders-management.payment-proof', $order->id) }}" alt="Bukti Pembayaran" class="img-fluid rounded border">
                </a>
                @else
                <p class="text-muted mb-0">Belum ada bukti pembayaran.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Workflow Pesanan</h6>
            </div>
            <div class="card-body">
                @php
                    $nextLabel = match($order->status) {
                        'pending' => 'Verifikasi Bayar',
                        'confirmed' => 'Sedang Diproses',
                        'processing' => ((float) $order->shipping_cost <= 0 ? 'Siap Diambil' : 'Dalam Perjalanan'),
                        'shipped' => 'Selesai',
                        default => 'Tidak ada tahap lanjutan',
                    };
                    $currentStatusLabel = match($order->status) {
                        'delivered' => 'Selesai',
                        default => strtoupper($order->status),
                    };
                    $canAdvance = $order->status !== 'cancelled';
                    $canCancel = !in_array($order->status, ['cancelled', 'delivered'], true);
                @endphp

                <div class="mb-3">
                    <div class="small text-muted">Status Pembayaran</div>
                    <span class="badge {{ $order->payment_status === 'success' ? 'bg-success' : ($order->payment_status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ $paymentStatusLabel }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="small text-muted">Status Tracking Saat Ini</div>
                    <span class="badge bg-info text-dark">{{ $currentStatusLabel }}</span>
                </div>

                <div class="mb-3">
                    <div class="small text-muted">Tahap Berikutnya</div>
                    <strong>{{ $nextLabel }}</strong>
                </div>

                <div class="d-grid gap-2">
                    <form action="{{ route('orders-management.advance', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary w-100" {{ $canAdvance ? '' : 'disabled' }}>
                            Lanjutkan Tahap
                        </button>
                    </form>

                    <form action="{{ route('orders-management.cancel', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100" {{ $canCancel ? '' : 'disabled' }}>
                            Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
