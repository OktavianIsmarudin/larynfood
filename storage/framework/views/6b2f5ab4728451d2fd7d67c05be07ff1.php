<?php $__env->startSection('title', 'Laryn - Sistem Inventory & Keuangan Bisnis Makanan'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Hero Carousel */
    .hero-carousel {
        position: relative;
        height: 600px;
        overflow: hidden;
        background: linear-gradient(135deg, #21489d 0%, #1a3a7d 100%);
    }

    .carousel-item-custom {
        height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background-size: cover;
        background-position: center;
    }

    .carousel-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(33, 72, 157, 0.9) 0%, rgba(26, 58, 125, 0.8) 100%);
    }

    .carousel-content {
        position: relative;
        z-index: 2;
        color: white;
        text-align: center;
        max-width: 800px;
        padding: 0 20px;
    }

    .carousel-content h1 {
        font-size: 56px;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .carousel-content p {
        font-size: 20px;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    .carousel-control-custom {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        z-index: 10;
        color: white;
        font-size: 20px;
    }

    .carousel-control-custom:hover {
        background: white;
        color: #21489d;
    }

    .carousel-control-prev-custom {
        left: 30px;
    }

    .carousel-control-next-custom {
        right: 30px;
    }

    /* Products Section */
    .products-section {
        padding: 80px 0;
        background: #F8F9FA;
    }

    .section-title {
        text-align: center;
        margin-bottom: 20px;
    }

    .section-title h2 {
        font-size: 42px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 10px;
    }

    .section-subtitle {
        text-align: center;
        color: #6C757D;
        font-size: 18px;
        margin-bottom: 50px;
    }

    /* Product Card - Toyota Style */
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        height: 100%;
        position: relative;
        border: 1px solid #E5E7EB;
    }

    .product-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
    }

    .product-badge-new {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #EF4444;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        z-index: 5;
    }

    .product-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .product-body {
        padding: 24px;
    }

    .product-name {
        font-size: 20px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 12px;
        min-height: 60px;
    }

    .product-price-label {
        font-size: 13px;
        color: #6C757D;
        margin-bottom: 5px;
    }

    .product-price {
        font-size: 18px;
        font-weight: 700;
        color: #21489d;
        margin-bottom: 15px;
    }

    .product-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge-category {
        display: inline-block;
        padding: 5px 12px;
        border: 1px solid #21489d;
        color: #21489d;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border: 1px solid #10B981;
        color: #10B981;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }

    /* Promo Banner */
    .promo-banner {
        background: linear-gradient(135deg, #21489d 0%, #1a3a7d 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 50px;
        text-align: center;
    }

    .promo-banner h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    /* About Section */
    .about-section {
        padding: 80px 0;
        background: white;
    }

    .feature-box {
        text-align: center;
        padding: 30px;
        transition: transform 0.3s;
    }

    .feature-box:hover {
        transform: translateY(-10px);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #21489d 0%, #1a3a7d 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 20px;
    }

    .feature-box h4 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #212529;
    }

    .feature-box p {
        color: #6C757D;
        line-height: 1.6;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero Carousel -->
<section class="hero-carousel" id="home">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="carousel-item-custom <?php echo e($index === 0 ? 'active' : ''); ?>">
                <div class="carousel-overlay"></div>
                <div class="carousel-content">
                    <h1><?php echo e($promo['title']); ?></h1>
                    <p><?php echo e($promo['subtitle']); ?></p>
                    <a id="heroOrderNowBtn" href="<?php echo e(url('/products')); ?>" class="btn btn-light btn-lg" style="padding: 15px 40px; font-weight: 600; border-radius: 8px;">
                        Pesan Sekarang <i class="fas fa-shopping-cart ms-2"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev-custom" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="carousel-control-next-custom" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</section>

<!-- Products Section -->
<section class="products-section" id="products">
    <div class="container">
        <!-- Promo Banner -->
        <div class="promo-banner">
            <h4>🎉 Harga Spesial Program "Untuk Semua" Mulai dari Rp50.000 🎉</h4>
        </div>

        <div class="section-title">
            <h2>Explore Produk</h2>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card">
                    <span class="product-badge-new">NEW!</span>

                    <div class="product-image">
                        <?php if($product->gambar_produk): ?>
                            <img src="<?php echo e(asset('storage/' . $product->gambar_produk)); ?>" alt="<?php echo e($product->nama_produk); ?>">
                        <?php else: ?>
                            <i class="fas fa-utensils" style="font-size: 80px; color: #D1D5DB;"></i>
                        <?php endif; ?>
                    </div>

                    <div class="product-body">
                        <h3 class="product-name"><?php echo e($product->nama_produk); ?></h3>

                        <?php if($product->user): ?>
                        <div class="text-muted mb-2" style="font-size: 12px;">
                            <i class="fas fa-store me-1"></i>
                            Oleh: <strong><?php echo e($product->user->name); ?></strong>
                        </div>
                        <?php endif; ?>

                        <div class="product-price-label">Mulai dari</div>
                        <div class="product-price">
                            Rp<?php echo e(number_format($product->harga_jual_per_pcs ?? $product->harga_jual, 0, ',', '.')); ?>

                        </div>

                        <div class="product-badges">
                            <?php if($product->tipe_produk === 'paket'): ?>
                                <span class="badge-type">
                                    <i class="fas fa-layer-group"></i> PAKET
                                </span>
                            <?php else: ?>
                                <span class="badge-type">
                                    <i class="fas fa-box"></i> SINGLE
                                </span>
                            <?php endif; ?>

                            <?php if($product->stockGudang && $product->stockGudang->category): ?>
                                <span class="badge-category"><?php echo e($product->stockGudang->category->nama_kategori); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <form action="<?php echo e(route('customer.cart.add', $product->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary-custom w-100">
                                    <i class="fas fa-shopping-cart me-2"></i> Beli Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open" style="font-size: 80px; color: #D1D5DB; margin-bottom: 20px;"></i>
                <p style="color: #6C757D; font-size: 18px;">Belum ada produk tersedia</p>
            </div>
            <?php endif; ?>
        </div>

        <?php if($products->count() > 0): ?>
        <div class="text-center mt-5">
            <a href="<?php echo e(route('products.explore')); ?>" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-th me-2"></i>Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- About Section -->
<section class="about-section" id="about">
    <div class="container">
        <div class="section-title">
            <h2>Mengapa Membeli Produk Kami?</h2>
        </div>
        <div class="section-subtitle">
            Kualitas terbaik dengan harga terjangkau untuk keluarga Anda
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4>Kualitas Terjamin</h4>
                    <p>Semua produk dipilih dengan teliti, segar, dan berkualitas premium untuk keluarga Anda</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4>Pengiriman Cepat</h4>
                    <p>Proses pesanan cepat dengan pengiriman yang aman dan tepat waktu ke rumah Anda</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h4>Harga Bersaing</h4>
                    <p>Dapatkan produk berkualitas dengan harga terbaik, hemat dan menyenangkan</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4>Pembayaran Mudah</h4>
                    <p>Berbagai metode pembayaran aman melalui Midtrans untuk kemudahan transaksi</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto slide carousel every 5 seconds
    const carousel = new bootstrap.Carousel(document.getElementById('heroCarousel'), {
        interval: 5000,
        ride: 'carousel'
    });

    // Ensure hero CTA always goes to explore products.
    const heroOrderNowBtn = document.getElementById('heroOrderNowBtn');
    if (heroOrderNowBtn) {
        heroOrderNowBtn.addEventListener('click', function(event) {
            event.preventDefault();
            window.location.assign('<?php echo e(url('/products')); ?>');
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Pongs\pengmas semester 8\sistem\sistem inventory\sistem inventory\resources\views/welcome.blade.php ENDPATH**/ ?>