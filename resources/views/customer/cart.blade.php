@extends('layouts.guest')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    .cart-section {
        padding: 80px 0;
        background: #F8F9FA;
        min-height: 100vh;
    }

    .cart-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .cart-item {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .cart-item-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        background: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-size: 20px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 10px;
    }

    .cart-item-price {
        font-size: 18px;
        color: #21489d;
        font-weight: 700;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-btn {
        width: 35px;
        height: 35px;
        border: 2px solid #21489d;
        background: white;
        color: #21489d;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .quantity-btn:hover {
        background: #21489d;
        color: white;
    }

    .quantity-input {
        width: 60px;
        text-align: center;
        border: 2px solid #DEE2E6;
        border-radius: 6px;
        padding: 5px;
    }

    .cart-summary {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 100px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #E5E7EB;
    }

    .summary-row:last-child {
        border-bottom: none;
        font-size: 20px;
        font-weight: 700;
        color: #21489d;
    }

    .btn-remove {
        color: #EF4444;
        border: 2px solid #EF4444;
        padding: 8px 20px;
        border-radius: 6px;
        background: white;
        transition: all 0.3s;
    }

    .btn-remove:hover {
        background: #EF4444;
        color: white;
    }
</style>
@endpush

@section('content')
<section class="cart-section">
    <div class="container">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
            <p class="text-muted">Kelola produk yang ingin Anda beli</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                @forelse($cartItems as $item)
                <div class="cart-item">
                    <div class="cart-item-image">
                        @if($item['product']->stockGudang && $item['product']->stockGudang->gambar_produk)
                            <img src="{{ asset('storage/' . $item['product']->stockGudang->gambar_produk) }}"
                                 alt="{{ $item['product']->nama_produk }}"
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        @else
                            <i class="fas fa-utensils" style="font-size: 50px; color: #D1D5DB;"></i>
                        @endif
                    </div>

                    <div class="cart-item-info">
                        <div class="cart-item-name">{{ $item['product']->nama_produk }}</div>
                        <div class="cart-item-price">Rp{{ number_format($item['product']->harga_jual_per_pcs, 0, ',', '.') }}</div>

                        <div class="quantity-control mt-3">
                            <button class="quantity-btn" onclick="updateQuantity({{ $item['product']->id }}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" id="qty-{{ $item['product']->id }}"
                                   value="{{ $item['quantity'] }}" min="1" readonly>
                            <button class="quantity-btn" onclick="updateQuantity({{ $item['product']->id }}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="mb-3">
                            <strong>Subtotal:</strong><br>
                            <span style="font-size: 20px; color: #21489d; font-weight: 700;">
                                Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                        <form action="{{ route('customer.cart.remove', $item['product']->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-remove">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="cart-item text-center py-5">
                    <i class="fas fa-shopping-cart" style="font-size: 80px; color: #D1D5DB; margin-bottom: 20px;"></i>
                    <h4>Keranjang Anda Kosong</h4>
                    <p class="text-muted">Tambahkan produk ke keranjang untuk melanjutkan</p>
                    <a href="{{ route('landing') }}" class="btn btn-primary-custom mt-3">
                        <i class="fas fa-shopping-bag me-2"></i> Belanja Sekarang
                    </a>
                </div>
                @endforelse
            </div>

            @if(count($cartItems) > 0)
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="mb-4">Ringkasan Belanja</h4>

                    <div class="summary-row">
                        <span>Total Produk</span>
                        <strong>{{ count($cartItems) }} item</strong>
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>Rp{{ number_format($total, 0, ',', '.') }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Ongkir</span>
                        <strong>Rp15.000</strong>
                    </div>

                    <div class="summary-row">
                        <span>Total Bayar</span>
                        <strong>Rp{{ number_format($total + 15000, 0, ',', '.') }}</strong>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="btn btn-primary-custom w-100 mt-4">
                        <i class="fas fa-credit-card me-2"></i> Lanjut ke Pembayaran
                    </a>

                    <a href="{{ route('landing') }}" class="btn btn-outline-custom w-100 mt-2">
                        <i class="fas fa-arrow-left me-2"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function updateQuantity(productId, change) {
    const input = document.getElementById('qty-' + productId);
    let currentQty = parseInt(input.value);
    let newQty = currentQty + change;

    if (newQty < 1) newQty = 1;

    input.value = newQty;

    // Send AJAX request to update cart
    fetch('{{ route("customer.cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id: productId,
            quantity: newQty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
