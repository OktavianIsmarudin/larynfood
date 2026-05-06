<?php $__env->startSection('title', 'Dashboard - Laryn'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" style="padding: 20px;">

    <?php if(isset($isSuperAdmin) && $isSuperAdmin): ?>
    <!-- Super Admin Filter Section -->
    <div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
        <div class="card-body" style="padding: 20px;">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-12 mb-3 mb-lg-0">
                    <div style="color: white;">
                        <h5 class="mb-1" style="font-weight: 700; font-size: 18px;">
                            <i class="fas fa-crown me-2"></i>Super Admin Dashboard
                        </h5>
                        <small style="opacity: 0.9; font-size: 13px;">Lihat data semua admin atau per admin</small>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12">
                    <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="row g-3">
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <select name="user_id" class="form-select" style="padding: 12px 16px; font-size: 14px; border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background-color: rgba(255,255,255,0.95); font-weight: 500;" onchange="this.form.submit()">
                                <option value="all" <?php echo e($selectedUserId === 'all' ? 'selected' : ''); ?>>
                                    📊 Mode Agregat - Semua Admin
                                </option>
                                <?php $__currentLoopData = $adminUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($admin->id); ?>" <?php echo e($selectedUserId == $admin->id ? 'selected' : ''); ?>>
                                        👤 <?php echo e($admin->name); ?> (<?php echo e($admin->email); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-light w-100" style="padding: 12px; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($selectedUserId === 'all'): ?>
                <div class="alert alert-light mt-3 mb-0" style="border-radius: 8px; background-color: rgba(255,255,255,0.95); border: none;">
                    <i class="fas fa-info-circle me-2" style="color: #8B5CF6;"></i>
                    <strong>Mode Agregat Aktif:</strong> Menampilkan gabungan data dari <strong>semua admin</strong> dalam sistem.
                </div>
            <?php else: ?>
                <?php
                    $selectedAdmin = $adminUsers->firstWhere('id', $selectedUserId);
                ?>
                <?php if($selectedAdmin): ?>
                    <div class="alert alert-light mt-3 mb-0" style="border-radius: 8px; background-color: rgba(255,255,255,0.95); border: none;">
                        <i class="fas fa-filter me-2" style="color: #8B5CF6;"></i>
                        <strong>Filter Aktif:</strong> Menampilkan data untuk admin <strong><?php echo e($selectedAdmin->name); ?></strong>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Row 1: Summary Cards (4 Cards Sejajar) -->
    <div class="row mb-4" style="gap: 0;">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stat-card blue" style="height: 100%; min-height: 140px; display: flex; flex-direction: column; justify-content: center;">
                <div class="row" style="margin: 0;">
                    <div class="col-8">
                        <h6 style="font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; opacity: 0.9;">Total Penjualan</h6>
                        <div class="stat-value" id="total-sales" style="font-size: 20px; font-weight: 700;">Rp <?php echo e(number_format($totalSales, 0, ',', '.')); ?></div>
                    </div>
                    <div class="col-4 stat-icon" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-cash-register" style="font-size: 32px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stat-card green" style="height: 100%; min-height: 140px; display: flex; flex-direction: column; justify-content: center;">
                <div class="row" style="margin: 0;">
                    <div class="col-8">
                        <h6 style="font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; opacity: 0.9;">Total Laba</h6>
                        <div class="stat-value" id="total-profit" style="font-size: 20px; font-weight: 700;">Rp <?php echo e(number_format($totalProfit, 0, ',', '.')); ?></div>
                    </div>
                    <div class="col-4 stat-icon" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 32px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stat-card orange" style="height: 100%; min-height: 140px; display: flex; flex-direction: column; justify-content: center;">
                <div class="row" style="margin: 0;">
                    <div class="col-8">
                        <h6 style="font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; opacity: 0.9;">Total Pembelian</h6>
                        <div class="stat-value" id="total-purchases" style="font-size: 20px; font-weight: 700;">Rp <?php echo e(number_format($totalPurchases, 0, ',', '.')); ?></div>
                    </div>
                    <div class="col-4 stat-icon" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart" style="font-size: 32px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stat-card red" style="height: 100%; min-height: 140px; display: flex; flex-direction: column; justify-content: center;">
                <div class="row" style="margin: 0;">
                    <div class="col-8">
                        <h6 style="font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; opacity: 0.9;">Saldo Modal</h6>
                        <div class="stat-value" id="total-saldo-modal" style="font-size: 20px; font-weight: 700;">Rp <?php echo e(number_format($totalSaldoModal, 0, ',', '.')); ?></div>
                    </div>
                    <div class="col-4 stat-icon" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-wallet" style="font-size: 32px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1B: Combined Modal/Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);">
                <div class="card-body" style="padding: 22px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-3 col-md-4">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: white; box-shadow: 0 8px 20px rgba(20, 184, 166, 0.25);">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 4px;">Saldo Modal + Penjualan</div>
                                    <div style="font-size: 20px; font-weight: 800; color: #0f172a;">Modal Tersedia</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-4">
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 999px; background: #ecfeff; color: #0f766e; font-size: 12px; font-weight: 700;">
                                    <i class="fas fa-piggy-bank"></i> Modal: Rp <?php echo e(number_format($totalSaldoModal, 0, ',', '.')); ?>

                                </span>
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 999px; background: #f0fdf4; color: #15803d; font-size: 12px; font-weight: 700;">
                                    <i class="fas fa-cash-register"></i> Penjualan Masuk: Rp <?php echo e(number_format($totalSales, 0, ',', '.')); ?>

                                </span>
                            </div>
                            <div style="margin-top: 10px; font-size: 12px; color: #64748b;">Card ini menampilkan modal tersisa yang ditambah uang penjualan masuk pada periode dashboard.</div>
                        </div>
                        <div class="col-lg-4 col-md-4 text-lg-end">
                            <div id="saldo-modal-bersih" style="font-size: 28px; font-weight: 800; color: <?php echo e($saldoModalBersih < 0 ? '#dc2626' : '#16a34a'); ?>; line-height: 1.1;">Rp <?php echo e(number_format($saldoModalBersih, 0, ',', '.')); ?></div>
                            <div style="margin-top: 6px; font-size: 12px; color: #94a3b8;">Modal + penjualan yang masuk</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Sales Chart & Top Products -->
    <div class="row mb-4">
        <!-- Sales Chart (Col-LG-8) -->
        <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); overflow: hidden; display: flex; flex-direction: column;">
                <div class="card-header" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); color: white; border: none; padding: 20px; flex-shrink: 0;">
                    <h5 class="mb-0" style="font-weight: 600; font-size: 15px;">
                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Grafik Penjualan & Laba (30 Hari Terakhir)
                    </h5>
                </div>
                <div class="card-body" style="padding: 25px; flex: 1; overflow: auto;">
                    <div style="position: relative; height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products (Col-LG-4) -->
        <div class="col-lg-4 col-md-12">
            <div class="card h-100" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); overflow: hidden; display: flex; flex-direction: column;">
                <div class="card-header" style="background: linear-gradient(135deg, #fbc02d 0%, #f57f17 100%); color: white; border: none; padding: 20px; flex-shrink: 0;">
                    <h5 class="mb-0" style="font-weight: 600; font-size: 15px;">
                        <i class="fas fa-star" style="margin-right: 10px;"></i> Top 5 Produk Terlaris
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px; flex: 1; overflow-y: auto; max-height: 400px;">
                    <?php if($topProducts->count() > 0): ?>
                        <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display: flex; align-items: center; margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #fbc02d;">
                            <div style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: linear-gradient(135deg, #fbc02d 0%, #f57f17 100%); color: white; border-radius: 50%; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                                <?php echo e($index + 1); ?>

                            </div>
                            <div style="margin-left: 12px; flex: 1; min-width: 0;">
                                <p style="font-weight: 600; margin: 0; color: #1a1a1a; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($product->produk->stockGudang->nama_produk ?? $product->produk->nama_produk ?? 'N/A'); ?></p>
                                <p style="font-size: 11px; color: #999; margin: 3px 0 0 0;"><?php echo e($product->total_qty ?? 0); ?> pcs terjual</p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 32px; opacity: 0.5; margin-bottom: 10px; display: block;"></i>
                            <p style="margin: 0; font-size: 13px;">Belum ada data penjualan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Three Charts (Equal Width) - Enhanced UI -->
    <div class="row mb-4">
        <!-- Penjualan per Kategori (Doughnut Chart) -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-3 mb-lg-0">
            <div class="card chart-card h-100">
                <div class="card-header chart-header chart-header-emerald">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="chart-icon-wrapper">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div>
                                <h5 class="chart-title mb-0">Penjualan per Kategori</h5>
                                <small class="chart-subtitle">30 hari terakhir</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body chart-body">
                    <?php if(count($salesByCategory) > 0): ?>
                        <div class="chart-container-doughnut">
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="chart-legend-custom mt-3" id="categoryLegend"></div>
                    <?php else: ?>
                        <div class="chart-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <p class="empty-text">Belum ada data penjualan</p>
                            <small class="empty-subtext">Data akan muncul setelah ada transaksi</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pendapatan vs Biaya (Bar Chart) -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-3 mb-lg-0">
            <div class="card chart-card h-100">
                <div class="card-header chart-header chart-header-ocean">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="chart-icon-wrapper">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <h5 class="chart-title mb-0">Pendapatan vs Biaya</h5>
                                <small class="chart-subtitle">Per minggu bulan ini</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body chart-body">
                    <div class="revenue-summary-row mb-3">
                        <div class="revenue-badge revenue-badge-income">
                            <span class="revenue-dot" style="background: #10b981;"></span>
                            <span>Pendapatan</span>
                        </div>
                        <div class="revenue-badge revenue-badge-cost">
                            <span class="revenue-dot" style="background: #ef4444;"></span>
                            <span>Biaya HPP</span>
                        </div>
                    </div>
                    <div class="chart-container-bar">
                        <canvas id="revenueVsCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Stock Produk (Horizontal Bar + Stats) -->
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card chart-card h-100">
                <div class="card-header chart-header chart-header-rose">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="chart-icon-wrapper">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div>
                                <h5 class="chart-title mb-0">Status Stok Produk</h5>
                                <small class="chart-subtitle"><?php echo e($stockStatus['total']); ?> produk terdaftar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body chart-body">
                    <?php if($stockStatus['total'] > 0): ?>
                        <!-- Stock Stats Cards -->
                        <div class="stock-stats-grid">
                            <div class="stock-stat-item stock-stat-safe">
                                <div class="stock-stat-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="stock-stat-info">
                                    <span class="stock-stat-value" id="stock-aman"><?php echo e($stockStatus['stok_aman']); ?></span>
                                    <span class="stock-stat-label">Aman</span>
                                </div>
                            </div>
                            <div class="stock-stat-item stock-stat-warning">
                                <div class="stock-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="stock-stat-info">
                                    <span class="stock-stat-value" id="stock-sedang"><?php echo e($stockStatus['stok_sedang']); ?></span>
                                    <span class="stock-stat-label">Sedang</span>
                                </div>
                            </div>
                            <div class="stock-stat-item stock-stat-danger">
                                <div class="stock-stat-icon"><i class="fas fa-times-circle"></i></div>
                                <div class="stock-stat-info">
                                    <span class="stock-stat-value" id="stock-rendah"><?php echo e($stockStatus['stok_rendah']); ?></span>
                                    <span class="stock-stat-label">Rendah</span>
                                </div>
                            </div>
                        </div>
                        <!-- Stock Bar Chart -->
                        <div class="chart-container-stock mt-3">
                            <canvas id="stockStatusChart"></canvas>
                        </div>
                    <?php else: ?>
                        <div class="chart-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <p class="empty-text">Belum ada produk</p>
                            <small class="empty-subtext">Tambahkan produk siap jual terlebih dahulu</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Quick Actions (Full Width) -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <div class="card-header" style="background: linear-gradient(135deg, #6a1b9a 0%, #7b1fa2 100%); color: white; border: none; padding: 20px; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="font-weight: 600; font-size: 15px;">
                        <i class="fas fa-lightning-bolt" style="margin-right: 10px;"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body" style="padding: 25px;">
                    <div class="row" style="margin: 0;">
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 px-2 mb-3">
                            <a href="<?php echo e(route('penjualan.create')); ?>" class="btn btn-sm w-100" style="padding: 12px; background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Penjualan
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 px-2 mb-3">
                            <a href="<?php echo e(route('pembelian.create')); ?>" class="btn btn-sm w-100" style="padding: 12px; background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Pembelian
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 px-2 mb-3">
                            <a href="<?php echo e(route('produk-siap-jual.create')); ?>" class="btn btn-sm w-100" style="padding: 12px; background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Produk
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 px-2 mb-3">
                            <a href="<?php echo e(route('suppliers.create')); ?>" class="btn btn-sm w-100" style="padding: 12px; background: linear-gradient(135deg, #c62828 0%, #e53935 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Supplier
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 px-2 mb-3">
                            <a href="<?php echo e(route('stock-gudang.create')); ?>" class="btn btn-sm w-100" style="padding: 12px; background: linear-gradient(135deg, #6a1b9a 0%, #7b1fa2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Stock
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Stat Card Styling */
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card.blue {
        background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
    }

    .stat-card.green {
        background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
    }

    .stat-card.orange {
        background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
    }

    .stat-card.red {
        background: linear-gradient(135deg, #fbc02d 0%, #f57f17 100%);
        color: rgba(0, 0, 0, 0.87);
    }

    .stat-card.red .stat-value {
        color: rgba(0, 0, 0, 0.87);
    }

    /* Card Height Consistency */
    .card.h-100 {
        height: 100%;
    }

    /* Chart Container */
    #salesChart, #categoryChart, #revenueVsCostChart, #stockStatusChart {
        max-height: 100%;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .col-lg-8, .col-lg-4 {
            margin-bottom: 15px;
        }
    }

    @media (max-width: 575px) {
        .stat-value {
            font-size: 16px !important;
        }

        .card-body {
            padding: 15px !important;
        }
    }

    /* Quick Actions Hover */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* ===== Enhanced Chart Card Styles ===== */
    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .chart-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
    }

    .chart-header {
        border: none;
        padding: 18px 22px;
        flex-shrink: 0;
        color: white;
    }
    .chart-header-emerald { background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%); }
    .chart-header-ocean { background: linear-gradient(135deg, #0369a1 0%, #0891b2 50%, #06b6d4 100%); }
    .chart-header-rose { background: linear-gradient(135deg, #be123c 0%, #e11d48 50%, #f43f5e 100%); }

    .chart-icon-wrapper {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        font-size: 16px;
        backdrop-filter: blur(4px);
    }
    .chart-title {
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0.2px;
    }
    .chart-subtitle {
        font-size: 11px;
        opacity: 0.8;
    }

    .chart-body {
        padding: 22px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #fff;
    }

    /* Chart Containers */
    .chart-container-doughnut {
        position: relative;
        width: 100%;
        height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-container-bar {
        position: relative;
        width: 100%;
        height: 220px;
    }
    .chart-container-stock {
        position: relative;
        width: 100%;
        height: 140px;
    }

    /* Custom Legend for Doughnut */
    .chart-legend-custom {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .legend-item {
        display: flex;
        align-items: center;
        padding: 4px 12px;
        background: #f8fafc;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        color: #475569;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .legend-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
        flex-shrink: 0;
    }

    /* Revenue Summary Badges */
    .revenue-summary-row {
        display: flex;
        justify-content: center;
        gap: 16px;
    }
    .revenue-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }
    .revenue-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    /* Stock Stats Grid */
    .stock-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .stock-stat-item {
        display: flex;
        align-items: center;
        padding: 12px 10px;
        border-radius: 12px;
        gap: 8px;
    }
    .stock-stat-safe {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #a7f3d0;
    }
    .stock-stat-warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fde68a;
    }
    .stock-stat-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        border: 1px solid #fca5a5;
    }
    .stock-stat-icon {
        font-size: 16px;
        flex-shrink: 0;
    }
    .stock-stat-safe .stock-stat-icon { color: #059669; }
    .stock-stat-warning .stock-stat-icon { color: #d97706; }
    .stock-stat-danger .stock-stat-icon { color: #dc2626; }
    .stock-stat-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    .stock-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
    }
    .stock-stat-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    /* Empty State */
    .chart-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        flex: 1;
    }
    .empty-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #94a3b8;
        margin-bottom: 16px;
    }
    .empty-text {
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin: 0 0 4px;
    }
    .empty-subtext {
        font-size: 12px;
        color: #94a3b8;
    }
</style>

<?php $__env->startSection('extra-js'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Chart Color Scheme
    const chartColors = {
        primary: '#0d47a1',
        success: '#2e7d32',
        danger: '#c62828',
        warning: '#fbc02d',
        info: '#00897b'
    };

    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesData = <?php echo json_encode($salesByDate); ?>;

    const salesLabels = salesData.map(d => new Date(d.date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
    const sales = salesData.map(d => parseFloat(d.total_sales));
    const profits = salesData.map(d => parseFloat(d.total_profit));

    window.salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [
                {
                    label: 'Total Penjualan',
                    data: sales,
                    borderColor: chartColors.primary,
                    backgroundColor: 'rgba(13, 71, 161, 0.08)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: chartColors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                },
                {
                    label: 'Laba',
                    data: profits,
                    borderColor: chartColors.success,
                    backgroundColor: 'rgba(46, 125, 50, 0.08)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: chartColors.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 13, weight: '600' },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                filler: {
                    propagate: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 12 },
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'K';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });

    // ===== Category Sales Chart (Doughnut) =====
    const categoryData = <?php echo json_encode($salesByCategory); ?>;
    if (categoryData.length > 0) {
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryColors = [
            { bg: 'rgba(16, 185, 129, 0.85)', border: '#10b981' },
            { bg: 'rgba(59, 130, 246, 0.85)', border: '#3b82f6' },
            { bg: 'rgba(245, 158, 11, 0.85)', border: '#f59e0b' },
            { bg: 'rgba(239, 68, 68, 0.85)', border: '#ef4444' },
            { bg: 'rgba(139, 92, 246, 0.85)', border: '#8b5cf6' },
            { bg: 'rgba(236, 72, 153, 0.85)', border: '#ec4899' },
            { bg: 'rgba(20, 184, 166, 0.85)', border: '#14b8a6' },
            { bg: 'rgba(249, 115, 22, 0.85)', border: '#f97316' },
        ];
        const catLabels = categoryData.map(d => d.kategori);
        const catValues = categoryData.map(d => parseFloat(d.total_sales));
        const catBg = categoryData.map((_, i) => categoryColors[i % categoryColors.length].bg);
        const catBorder = categoryData.map((_, i) => categoryColors[i % categoryColors.length].border);

        window.categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catBg,
                    borderColor: catBorder,
                    borderWidth: 2,
                    hoverOffset: 8,
                    spacing: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true,
                        boxPadding: 6,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((ctx.raw / total) * 100).toFixed(1);
                                return ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID') + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Build custom legend
        const legendContainer = document.getElementById('categoryLegend');
        if (legendContainer) {
            catLabels.forEach((label, i) => {
                const total = catValues.reduce((a, b) => a + b, 0);
                const pct = total > 0 ? ((catValues[i] / total) * 100).toFixed(0) : 0;
                const item = document.createElement('div');
                item.className = 'legend-item';
                item.innerHTML = '<span class="legend-dot" style="background:' + catBorder[i] + ';"></span>' + label + ' <span style="color:#94a3b8;margin-left:4px;">(' + pct + '%)</span>';
                legendContainer.appendChild(item);
            });
        }
    }

    // ===== Revenue vs Cost Chart (Bar) =====
    const revenueData = <?php echo json_encode($weeklyRevenueVsCost); ?>;
    const revenueCtx = document.getElementById('revenueVsCostChart').getContext('2d');

    // Gradient for Revenue bars
    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 250);
    revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
    revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0.4)');

    // Gradient for Cost bars
    const costGradient = revenueCtx.createLinearGradient(0, 0, 0, 250);
    costGradient.addColorStop(0, 'rgba(239, 68, 68, 0.9)');
    costGradient.addColorStop(1, 'rgba(239, 68, 68, 0.4)');

    window.revenueVsCostChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueData.map(d => d.label),
            datasets: [
                {
                    label: 'Pendapatan',
                    data: revenueData.map(d => d.revenue),
                    backgroundColor: revenueGradient,
                    borderColor: '#10b981',
                    borderWidth: 0,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: false,
                    barPercentage: 0.7,
                    categoryPercentage: 0.65,
                },
                {
                    label: 'Biaya HPP',
                    data: revenueData.map(d => d.cost),
                    backgroundColor: costGradient,
                    borderColor: '#ef4444',
                    borderWidth: 0,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: false,
                    barPercentage: 0.7,
                    categoryPercentage: 0.65,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    boxPadding: 6,
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 11, weight: '500' },
                        color: '#94a3b8',
                        padding: 8,
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 11, weight: '600' },
                        color: '#64748b',
                        padding: 4
                    }
                }
            }
        }
    });

    // ===== Stock Status Chart (Horizontal Bar) =====
    const stockData = <?php echo json_encode($stockStatus); ?>;
    if (stockData.total > 0) {
        const stockCtx = document.getElementById('stockStatusChart').getContext('2d');

        window.stockStatusChart = new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: ['Aman (≥10)', 'Sedang (3-9)', 'Rendah (<3)'],
                datasets: [{
                    label: 'Jumlah Produk',
                    data: [stockData.stok_aman, stockData.stok_sedang, stockData.stok_rendah],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.65,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                return ctx.raw + ' produk';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        border: { display: false },
                        ticks: {
                            font: { size: 11, weight: '500' },
                            color: '#94a3b8',
                            stepSize: 1,
                            padding: 4
                        }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#475569',
                            padding: 8
                        }
                    }
                }
            }
        });
    }

    // Auto-refresh dashboard statistics every 30 seconds
    function refreshDashboardStats() {
        fetch('<?php echo e(route("dashboard.stats")); ?>')
            .then(response => response.json())
            .then(data => {
                // Update stat cards with animation
                updateStatCard('total-sales', data.totalSales);
                updateStatCard('total-profit', data.totalProfit);
                updateStatCard('total-purchases', data.totalPurchases);
                updateStatCard('total-saldo-modal', data.totalSaldoModal);
                updateStatCard('saldo-modal-bersih', data.saldoModalBersih);

                // Update sales line chart
                if (data.salesByDate && window.salesChart) {
                    const newLabels = data.salesByDate.map(d => new Date(d.date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
                    const newSales = data.salesByDate.map(d => parseFloat(d.total_sales));
                    const newProfits = data.salesByDate.map(d => parseFloat(d.total_profit));

                    window.salesChart.data.labels = newLabels;
                    window.salesChart.data.datasets[0].data = newSales;
                    window.salesChart.data.datasets[1].data = newProfits;
                    window.salesChart.update('none');
                }

                // Update category chart
                if (data.salesByCategory && window.categoryChart) {
                    const catValues = data.salesByCategory.map(d => parseFloat(d.total_sales));
                    window.categoryChart.data.datasets[0].data = catValues;
                    window.categoryChart.update('none');
                }

                // Update revenue vs cost chart
                if (data.weeklyRevenueVsCost && window.revenueVsCostChart) {
                    window.revenueVsCostChart.data.datasets[0].data = data.weeklyRevenueVsCost.map(d => d.revenue);
                    window.revenueVsCostChart.data.datasets[1].data = data.weeklyRevenueVsCost.map(d => d.cost);
                    window.revenueVsCostChart.update('none');
                }

                // Update stock status chart & stats
                if (data.stockStatus && window.stockStatusChart) {
                    window.stockStatusChart.data.datasets[0].data = [data.stockStatus.stok_aman, data.stockStatus.stok_sedang, data.stockStatus.stok_rendah];
                    window.stockStatusChart.update('none');

                    const safeEl = document.getElementById('stock-aman');
                    const warnEl = document.getElementById('stock-sedang');
                    const dangerEl = document.getElementById('stock-rendah');
                    if (safeEl) safeEl.textContent = data.stockStatus.stok_aman;
                    if (warnEl) warnEl.textContent = data.stockStatus.stok_sedang;
                    if (dangerEl) dangerEl.textContent = data.stockStatus.stok_rendah;
                }
            })
            .catch(error => console.log('Dashboard auto-refresh: ', error));
    }

    // Helper function to update stat cards with animation
    function updateStatCard(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            const formattedValue = 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);

            if (element.textContent !== formattedValue) {
                element.style.opacity = '0.5';
                setTimeout(() => {
                    element.textContent = formattedValue;
                    element.style.opacity = '1';
                }, 150);
            }
        }
    }

    // Set interval for auto-refresh (30 seconds)
    setInterval(refreshDashboardStats, 30000);
</script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Pongs\pengmas semester 8\sistem\sistem inventory\sistem inventory\resources\views/dashboard.blade.php ENDPATH**/ ?>