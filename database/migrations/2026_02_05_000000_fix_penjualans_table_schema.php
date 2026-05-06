<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Drop harga_jual if it exists (will be replaced by harga_satuan)
            if (Schema::hasColumn('penjualans', 'harga_jual') && !Schema::hasColumn('penjualans', 'harga_satuan')) {
                $table->dropColumn('harga_jual');
            }
            
            // Add harga_satuan if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'harga_satuan')) {
                $table->decimal('harga_satuan', 12, 2)->nullable()->after('qty_pcs');
            }
            
            // Add total_penjualan if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'total_penjualan')) {
                $table->decimal('total_penjualan', 12, 2)->default(0)->after('harga_satuan');
            }
            
            // Add jumlah_pcs if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'jumlah_pcs')) {
                $table->integer('jumlah_pcs')->default(0)->after('qty_pcs');
            }
            
            // Add tanggal_penjualan if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'tanggal_penjualan')) {
                $table->date('tanggal_penjualan')->nullable()->after('user_id');
            }
            
            // Add nama_customer_snapshot if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'nama_customer_snapshot')) {
                $table->string('nama_customer_snapshot')->nullable()->after('customer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Rename back produk_siap_jual_id to produk_id
            if (Schema::hasColumn('penjualans', 'produk_siap_jual_id')) {
                $table->renameColumn('produk_siap_jual_id', 'produk_id');
            }
            
            // Rename back harga_satuan to harga_jual
            if (Schema::hasColumn('penjualans', 'harga_satuan')) {
                $table->renameColumn('harga_satuan', 'harga_jual');
            }
            
            // Drop added columns
            if (Schema::hasColumn('penjualans', 'total_penjualan')) {
                $table->dropColumn('total_penjualan');
            }
            if (Schema::hasColumn('penjualans', 'jumlah_pcs')) {
                $table->dropColumn('jumlah_pcs');
            }
            if (Schema::hasColumn('penjualans', 'tanggal_penjualan')) {
                $table->dropColumn('tanggal_penjualan');
            }
            if (Schema::hasColumn('penjualans', 'nama_customer_snapshot')) {
                $table->dropColumn('nama_customer_snapshot');
            }
        });
    }
};
