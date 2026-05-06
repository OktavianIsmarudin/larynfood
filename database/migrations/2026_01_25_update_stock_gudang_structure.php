<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to stock_gudang if they don't exist
        Schema::table('stock_gudang', function (Blueprint $table) {
            // Foreign key untuk pembelian
            if (!Schema::hasColumn('stock_gudang', 'purchase_id')) {
                $table->foreignId('purchase_id')->nullable()->unique()->constrained('pembelians')->onUpdate('cascade')->onDelete('cascade')->after('id');
            }
            
            // Rename & reorganize columns untuk match requirement
            if (Schema::hasColumn('stock_gudang', 'jumlah_stock')) {
                // Will keep jumlah_stock but add jumlah_pack for clarity
                $table->integer('jumlah_pack')->nullable()->after('konversi_satuan');
            }
            
            // Add pcs tracking columns
            if (!Schema::hasColumn('stock_gudang', 'pcs_terpakai')) {
                $table->integer('pcs_terpakai')->default(0)->after('jumlah_pack');
            }
            
            if (!Schema::hasColumn('stock_gudang', 'pcs_sisa')) {
                $table->integer('pcs_sisa')->nullable()->after('pcs_terpakai');
            }
            
            if (!Schema::hasColumn('stock_gudang', 'total_pcs')) {
                $table->integer('total_pcs')->nullable()->after('pcs_sisa');
            }
            
            // Source tracking
            if (!Schema::hasColumn('stock_gudang', 'source')) {
                $table->enum('source', ['pembelian', 'manual'])->default('manual')->after('total_pcs');
            }
            
            // Status tracking untuk pembelian
            if (!Schema::hasColumn('stock_gudang', 'status_stock')) {
                $table->enum('status_stock', ['belum_masuk_gudang', 'sudah_masuk_gudang'])->default('sudah_masuk_gudang')->after('source');
            }
            
            // Rename lokasi gudang
            if (Schema::hasColumn('stock_gudang', 'gudang_penyimpanan') && !Schema::hasColumn('stock_gudang', 'lokasi_gudang')) {
                $table->renameColumn('gudang_penyimpanan', 'lokasi_gudang');
            }
            
            // Rename harga
            if (Schema::hasColumn('stock_gudang', 'harga_beli') && !Schema::hasColumn('stock_gudang', 'harga_beli_pack')) {
                $table->renameColumn('harga_beli', 'harga_beli_pack');
            }
            
            // Rename satuan
            if (Schema::hasColumn('stock_gudang', 'satuan_utama') && !Schema::hasColumn('stock_gudang', 'satuan')) {
                $table->renameColumn('satuan_utama', 'satuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            // Drop foreign key dan columns
            if (Schema::hasColumn('stock_gudang', 'purchase_id')) {
                $table->dropForeign(['purchase_id']);
                $table->dropColumn('purchase_id');
            }
            
            if (Schema::hasColumn('stock_gudang', 'jumlah_pack')) {
                $table->dropColumn('jumlah_pack');
            }
            
            if (Schema::hasColumn('stock_gudang', 'pcs_terpakai')) {
                $table->dropColumn('pcs_terpakai');
            }
            
            if (Schema::hasColumn('stock_gudang', 'pcs_sisa')) {
                $table->dropColumn('pcs_sisa');
            }
            
            if (Schema::hasColumn('stock_gudang', 'total_pcs')) {
                $table->dropColumn('total_pcs');
            }
            
            if (Schema::hasColumn('stock_gudang', 'source')) {
                $table->dropColumn('source');
            }
            
            if (Schema::hasColumn('stock_gudang', 'status_stock')) {
                $table->dropColumn('status_stock');
            }
            
            // Rename back
            if (Schema::hasColumn('stock_gudang', 'lokasi_gudang')) {
                $table->renameColumn('lokasi_gudang', 'gudang_penyimpanan');
            }
            
            if (Schema::hasColumn('stock_gudang', 'harga_beli_pack')) {
                $table->renameColumn('harga_beli_pack', 'harga_beli');
            }
            
            if (Schema::hasColumn('stock_gudang', 'satuan')) {
                $table->renameColumn('satuan', 'satuan_utama');
            }
        });
    }
};
