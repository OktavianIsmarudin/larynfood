@extends('layouts.guest')

@section('title', 'Pembayaran')

@push('styles')
<style>
    .checkout-section {
        padding: 80px 0;
        background: #F8F9FA;
        min-height: 100vh;
    }

    .checkout-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .form-label {
        font-weight: 600;
        color: #212529;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 2px solid #DEE2E6;
        border-radius: 8px;
        padding: 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #21489d;
        box-shadow: 0 0 0 0.2rem rgba(33, 72, 157, 0.25);
    }

    .order-summary {
        background: #F8F9FA;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #DEE2E6;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        padding: 20px 0;
        font-size: 20px;
        font-weight: 700;
        color: #21489d;
        border-top: 2px solid #21489d;
        margin-top: 15px;
    }

    .payment-card {
        border: 2px dashed #C9D6F4;
        border-radius: 10px;
        padding: 16px;
        background: #F7FAFF;
    }

    .payment-proof-preview {
        width: 100%;
        max-height: 220px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid #DEE2E6;
        background: #fff;
        display: none;
    }
</style>
@endpush

@section('content')
<section class="checkout-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h3 class="mb-4"><i class="fas fa-receipt"></i> Pembayaran Pesanan</h3>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Langkah 1:</strong> Upload bukti transfer terlebih dahulu.<br>
                            <small>Setelah upload, lanjutkan isi data pemesanan.</small>
                        </div>
                        <button type="button" class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-qrcode me-2"></i> Lihat QRIS & Rekening
                        </button>
                    </div>

                    <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkoutForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3 payment-card">
                            <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" id="payment_proof" name="payment_proof" class="form-control" accept="image/*" required>
                            <small class="text-muted">Upload foto bukti transfer (JPG/PNG/WEBP, maksimal 4 MB)</small>
                            <img id="payment-proof-preview" class="payment-proof-preview mt-3" alt="Preview bukti pembayaran">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control"
                                       value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email (Opsional)</label>
                                <input type="email" name="customer_email" class="form-control"
                                       value="{{ old('customer_email') }}" placeholder="contoh@email.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" class="form-control"
                                   value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Opsi Pengiriman <span class="text-danger">*</span></label>
                            <select name="delivery_method" id="delivery_method" class="form-select" required>
                                <option value="">Pilih opsi pengiriman</option>
                                <option value="pickup" {{ old('delivery_method') === 'pickup' ? 'selected' : '' }}>Ambil di Tempat</option>
                                <option value="delivery" {{ old('delivery_method') === 'delivery' ? 'selected' : '' }}>Dikirim</option>
                            </select>
                        </div>

                        <div class="mb-3" id="shipping-address-group">
                            <label class="form-label">Alamat Pengiriman Lengkap <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" class="form-control" rows="4"
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                      >{{ old('shipping_address') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Catatan tambahan untuk penjual...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-shield-alt"></i>
                            Bukti transfer akan dicek admin terlebih dahulu. Tracking pesanan berjalan setelah verifikasi.
                        </div>

                        <button type="submit" class="btn btn-primary-custom btn-lg w-100" id="payButton">
                            <i class="fas fa-paper-plane me-2"></i> Pesan Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-card">
                    <h4 class="mb-4">Ringkasan Pesanan</h4>

                    <div class="order-summary">
                        @foreach($cartItems as $item)
                        <div class="order-item">
                            <div>
                                <strong>{{ $item['product']->nama_produk }}</strong><br>
                                <small class="text-muted">{{ $item['quantity'] }} x Rp{{ number_format($item['product']->harga_jual_per_pcs, 0, ',', '.') }}</small>
                            </div>
                            <div class="text-end">
                                <strong>Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span>Subtotal</span>
                        <strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span>Ongkos Kirim</span>
                        <strong id="shipping-cost-text">Rp{{ number_format($shippingCost, 0, ',', '.') }}</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total Bayar</span>
                        <span id="grand-total-text">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('customer.cart') }}" class="btn btn-outline-custom w-100">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
                </a>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel"><i class="fas fa-money-check-alt me-2"></i>Informasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="payment-card h-100">
                            <h6 class="fw-bold mb-3">QRIS</h6>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=Laryn-Pembayaran" alt="QRIS Laryn" class="img-fluid rounded border">
                            <small class="d-block mt-2 text-muted">Scan QRIS lalu upload bukti transfer.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="payment-card h-100">
                            <h6 class="fw-bold mb-3">Transfer Bank</h6>
                            <p class="mb-1"><strong>Bank BCA</strong></p>
                            <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                            <p class="mb-1">Atas Nama: <strong>Laryn</strong></p>
                            <hr>
                            <p class="mb-1"><strong>Bank Mandiri</strong></p>
                            <p class="mb-1">No. Rekening: <strong>9876543210</strong></p>
                            <p class="mb-0">Atas Nama: <strong>Laryn</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Saya Sudah Transfer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const deliveryMethodInput = document.getElementById('delivery_method');
const shippingAddressGroup = document.getElementById('shipping-address-group');
const shippingCostText = document.getElementById('shipping-cost-text');
const grandTotalText = document.getElementById('grand-total-text');
const subtotalValue = {{ (int) $subtotal }};
const shippingAddressInput = document.querySelector('textarea[name="shipping_address"]');

function syncDeliveryUI() {
    const isDelivery = deliveryMethodInput.value === 'delivery';
    shippingAddressGroup.style.display = isDelivery ? 'block' : 'none';
    shippingAddressInput.required = isDelivery;

    const shippingCost = isDelivery ? 15000 : 0;
    const total = subtotalValue + shippingCost;

    shippingCostText.textContent = `Rp${shippingCost.toLocaleString('id-ID')}`;
    grandTotalText.textContent = `Rp${total.toLocaleString('id-ID')}`;
}

deliveryMethodInput.addEventListener('change', syncDeliveryUI);
syncDeliveryUI();

const paymentProofInput = document.getElementById('payment_proof');
const paymentProofPreview = document.getElementById('payment-proof-preview');

paymentProofInput.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (!file) {
        paymentProofPreview.style.display = 'none';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        paymentProofPreview.src = e.target.result;
        paymentProofPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});

document.getElementById('checkoutForm').addEventListener('submit', function() {
    const payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
});
</script>
@endpush
