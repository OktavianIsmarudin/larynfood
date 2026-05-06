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
        // Add fields to stock_gudang if they don't exist
        if (Schema::hasTable('stock_gudang') && !Schema::hasColumn('stock_gudang', 'harga_beli_pcs')) {
            Schema::table('stock_gudang', function (Blueprint $table) {
                $table->decimal('harga_beli_pcs', 12, 2)->nullable()->after('harga_beli');
            });
        }

        // Add fields to produk_siap_juals for stock tracking
        if (Schema::hasTable('produk_siap_juals') && !Schema::hasColumn('produk_siap_juals', 'jumlah_pcs')) {
            Schema::table('produk_siap_juals', function (Blueprint $table) {
                $table->integer('jumlah_pcs')->default(0)->after('stok_pcs');
                $table->decimal('total_hpp_modal', 12, 2)->default(0)->after('harga_jual_per_pcs');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock_gudang')) {
            Schema::table('stock_gudang', function (Blueprint $table) {
                if (Schema::hasColumn('stock_gudang', 'harga_beli_pcs')) {
                    $table->dropColumn('harga_beli_pcs');
                }
            });
        }

        if (Schema::hasTable('produk_siap_juals')) {
            Schema::table('produk_siap_juals', function (Blueprint $table) {
                if (Schema::hasColumn('produk_siap_juals', 'jumlah_pcs')) {
                    $table->dropColumn(['jumlah_pcs', 'total_hpp_modal']);
                }
            });
        }
    }
};
