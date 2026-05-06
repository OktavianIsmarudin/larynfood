@extends('layouts.guest')

@section('title', 'Explore Produk')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5 mt-4">
        <h1 class="fw-bold mb-3">
            <i class="fas fa-shopping-bag me-2" style="color: #21489d;"></i>
            Explore Produk Kami
        </h1>
        <p class="text-muted">Temukan produk makanan berkualitas terbaik untuk keluarga Anda</p>
    </div>

    <!-- Search & Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('products.explore') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-search me-1"></i>Cari Produk
                        </label>
                        <input type="text"
                               class="form-control"
                               name="search"
                               placeholder="Cari nama produk..."
                               value="{{ request('search') }}">
                    </div>

                    <!-- Min Price -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-tag me-1"></i>Harga Min
                        </label>
                        <input type="number"
                               class="form-control"
                               name="min_price"
                               placeholder="0"
                               value="{{ request('min_price') }}">
                    </div>

                    <!-- Max Price -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-tag me-1"></i>Harga Max
                        </label>
                        <input type="number"
                               class="form-control"
                               name="max_price"
                               placeholder="1000000"
                               value="{{ request('max_price') }}">
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-sort me-1"></i>Urutkan
                        </label>
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                            <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('products.explore') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted">Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk</span>
        </div>
        @if(request('search') || request('min_price') || request('max_price'))
        <div>
            <a href="{{ route('products.explore') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times me-1"></i>Hapus Filter
            </a>
        </div>
        @endif
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="row g-4 mb-5">
        @foreach($products as $product)
        @php
            $availablePaket = (int) ($product->stok_siap_jual ?? 0);
            $pcsPerPaket = max(1, (int) ($product->pcs_per_paket ?? 1));
            $availablePcs = $availablePaket * $pcsPerPaket;
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 shadow-sm hover-lift">
                <!-- Product Image -->
                <div class="position-relative" style="height: 200px; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    @if($product->gambar_produk)
                        <img src="{{ asset('storage/' . $product->gambar_produk) }}"
                             alt="{{ $product->nama_produk }}"
                             class="w-100 h-100"
                             style="object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="fas fa-drumstick-bite fa-4x text-white opacity-50"></i>
                        </div>
                    @endif
                    @if($availablePaket > 0)
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                        <i class="fas fa-check me-1"></i>Tersedia
                    </span>
                    @else
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                        <i class="fas fa-times me-1"></i>Stok Habis
                    </span>
                    @endif
                </div>

                <div class="card-body">
                    <!-- Product Name -->
                    <h5 class="card-title mb-2 text-truncate" title="{{ $product->nama_produk }}">
                        {{ $product->nama_produk }}
                    </h5>

                    <!-- Seller Info -->
                    @if($product->user)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-store me-1"></i>
                        {{ $product->user->name }}
                    </p>
                    @endif

                    <!-- Price -->
                    <div class="mb-3">
                        <span class="h4 text-primary fw-bold mb-0">
                            Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                        </span>
                        <span class="text-muted small">/paket</span>
                    </div>

                    <!-- Stock Info -->
                    <p class="text-muted small mb-3">
                        <i class="fas fa-box me-1"></i>
                        Stok: {{ $availablePaket }} paket ({{ $availablePcs }} pcs)
                    </p>

                    <!-- Action Button -->
                    @if($availablePaket > 0)
                        <form action="{{ route('customer.cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-ban me-2"></i>Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Produk tidak ditemukan</h4>
        <p class="text-muted mb-4">Coba ubah filter atau kata kunci pencarian Anda</p>
        <a href="{{ route('products.explore') }}" class="btn btn-primary">
            <i class="fas fa-redo me-2"></i>Reset Filter
        </a>
    </div>
    @endif
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.card-title {
    color: #2d3748;
    font-weight: 600;
}

.btn-primary {
    background: linear-gradient(135deg, #21489d 0%, #1a3a7d 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1a3a7d 0%, #21489d 100%);
}
</style>
@endsection
