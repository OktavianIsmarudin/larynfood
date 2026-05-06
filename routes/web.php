<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockGudangController;
use App\Http\Controllers\ProdukSiapJualController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukPaketController;
use App\Http\Controllers\NilaiGiziController;
use App\Http\Controllers\SaldoModalController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ChatbotController;

// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/products', [OrderController::class, 'explore'])->name('products.explore');
Route::get('/chatbot/profiles', [ChatbotController::class, 'profiles'])->name('chatbot.profiles');
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');

// Auth Routes (No Auth Required)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // Reports
    Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('/reports/sales-summary', [ReportController::class, 'salesSummary'])->name('reports.sales-summary');
    Route::get('/reports/purchase-summary', [ReportController::class, 'purchaseSummary'])->name('reports.purchase-summary');

    // Financial Reports
    Route::get('/financial-report', [FinancialReportController::class, 'index'])->name('financial-report.index');
    Route::get('/financial-report/filter/harian', [FinancialReportController::class, 'filterHarian'])->name('financial-report.filter-harian');
    Route::get('/financial-report/filter/bulanan', [FinancialReportController::class, 'filterBulanan'])->name('financial-report.filter-bulanan');
    Route::get('/financial-report/chart-data', [FinancialReportController::class, 'getChartData'])->name('financial-report.chart-data');
    Route::get('/financial-report/export-pdf', [FinancialReportController::class, 'exportPDF'])->name('financial-report.export-pdf');
    Route::get('/financial-report/export-excel', [FinancialReportController::class, 'exportExcel'])->name('financial-report.export-excel');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Master Data Routes
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('knowledge-base', KnowledgeBaseController::class)->except(['show']);

    // Inventory Routes
    Route::resource('stock-gudang', StockGudangController::class);
    Route::post('/stock-gudang/{stockGudang}/reduce-stock', [StockGudangController::class, 'reduceStock'])->name('stock-gudang.reduce-stock');
    Route::delete('/stock-adjustment/{stockAdjustment}', [StockGudangController::class, 'deleteStockAdjustment'])->name('stock-adjustment.delete');

    // AJAX & Special routes MUST be before resource routes
    Route::get('/produk-siap-jual/ajax/available-equipment', [ProdukSiapJualController::class, 'getAvailableEquipment'])->name('produk-siap-jual.available-equipment');

    Route::resource('produk-siap-jual', ProdukSiapJualController::class);
    Route::post('/produk-siap-jual/{produkSiapJual}/tambah-stock', [ProdukSiapJualController::class, 'tambahStock'])->name('produk-siap-jual.tambah-stock');
    Route::post('/produk-siap-jual/{produkSiapJual}/process-equipment', [ProdukSiapJualController::class, 'processEquipment'])->name('produk-siap-jual.process-equipment');
    Route::post('/produk-siap-jual/{produkSiapJual}/toggle-publish', [ProdukSiapJualController::class, 'togglePublish'])->name('produk-siap-jual.toggle-publish');

    // Produk Paket/Platter Routes
    Route::get('/produk-paket/{produkPaket}/details', [ProdukPaketController::class, 'getDetails'])->name('produk-paket.details');
    Route::post('/produk-paket/{produkPaket}/cek-stok', [ProdukPaketController::class, 'cekStok'])->name('produk-paket.cek-stok');
    Route::resource('produk-paket', ProdukPaketController::class);

    // Bill of Material (BOM) Routes
    Route::prefix('bom')->name('bom.')->group(function () {
        Route::get('/', [BomController::class, 'index'])->name('index');
        Route::get('/{id}/details', [BomController::class, 'details'])->name('details');
        Route::get('/export/excel', [BomController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{id}/export', [BomController::class, 'exportBom'])->name('export-bom');
        Route::get('/api/stats', [BomController::class, 'getStats'])->name('stats');
    });

    // Nilai Gizi (Nutrition) Routes
    Route::get('/nilai-gizi', [NilaiGiziController::class, 'index'])->name('nilai-gizi.index');
    Route::get('/nilai-gizi/{stockGudang}', [NilaiGiziController::class, 'show'])->name('nilai-gizi.show');
    Route::get('/nilai-gizi/{stockGudang}/edit', [NilaiGiziController::class, 'edit'])->name('nilai-gizi.edit');
    Route::put('/nilai-gizi/{stockGudang}', [NilaiGiziController::class, 'update'])->name('nilai-gizi.update');

    // Transaction Routes
    Route::resource('pembelian', PembelianController::class);
    Route::post('/pembelian/{pembelian}/to-stock-gudang', [PembelianController::class, 'toStockGudang'])->name('pembelian.to-stock-gudang');
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/{penjualan}/print-resi', [PenjualanController::class, 'printResi'])->name('penjualan.print-resi');
    Route::get('/penjualan/{penjualan}/download-resi', [PenjualanController::class, 'downloadResi'])->name('penjualan.download-resi');

    // Saldo Modal / Kas Usaha Routes
    Route::get('/saldo-modal', [SaldoModalController::class, 'index'])->name('saldo-modal.index');
    Route::get('/saldo-modal/create', [SaldoModalController::class, 'create'])->name('saldo-modal.create');
    Route::post('/saldo-modal', [SaldoModalController::class, 'store'])->name('saldo-modal.store');
    Route::delete('/saldo-modal/{saldoModal}', [SaldoModalController::class, 'destroy'])->name('saldo-modal.destroy');
    Route::get('/saldo-modal/penggunaan/create', [SaldoModalController::class, 'createPenggunaan'])->name('saldo-modal.penggunaan.create');
    Route::post('/saldo-modal/penggunaan', [SaldoModalController::class, 'storePenggunaan'])->name('saldo-modal.penggunaan.store');
    Route::delete('/saldo-modal/penggunaan/{penggunaanModal}', [SaldoModalController::class, 'destroyPenggunaan'])->name('saldo-modal.penggunaan.destroy');
    Route::get('/saldo-modal/{saldoModal}/remaining', [SaldoModalController::class, 'getRemainingSaldo'])->name('saldo-modal.remaining');

    // Super Admin Only Routes
    Route::middleware('superadmin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

    // Admin Order Management
    Route::prefix('orders-management')->name('orders-management.')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'adminShow'])->name('show');
        Route::get('/{order}/payment-proof', [OrderController::class, 'adminPaymentProof'])->name('payment-proof');
        Route::patch('/{order}/advance', [OrderController::class, 'adminAdvanceStage'])->name('advance');
        Route::patch('/{order}/cancel', [OrderController::class, 'adminCancel'])->name('cancel');
        Route::patch('/{order}/tracking', [OrderController::class, 'adminUpdateTracking'])->name('tracking.update');
        Route::patch('/{order}/payment-verify', [OrderController::class, 'adminVerifyPayment'])->name('payment.verify');
    });
});

// Customer Order Routes (Guest Allowed)
Route::prefix('customer')->name('customer.')->group(function () {
    // Cart
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::get('/cart/data', [OrderController::class, 'cartData'])->name('cart.data');
    Route::post('/cart/add/{id}', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [OrderController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processCheckout'])->name('checkout.process');

    // Tracking and orders by order number
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/track', [OrderController::class, 'trackLookup'])->name('track.lookup');
    Route::get('/track/{orderNumber}', [OrderController::class, 'track'])->name('track');
    Route::get('/track/{orderNumber}/status', [OrderController::class, 'trackStatus'])->name('track.status');
    Route::get('/orders/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
});

// Midtrans Callback (No Auth Required)
Route::post('/midtrans/callback', [OrderController::class, 'callback'])->name('midtrans.callback');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
