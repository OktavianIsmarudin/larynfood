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
            // Add new fields if they don't exist
            if (!Schema::hasColumn('penjualans', 'produk_siap_jual_id')) {
                $table->foreignId('produk_siap_jual_id')->nullable()->after('customer_id')->constrained('produk_siap_juals')->onUpdate('cascade')->onDelete('set null');
            }

            if (!Schema::hasColumn('penjualans', 'harga_satuan')) {
                $table->decimal('harga_satuan', 12, 2)->default(0)->after('qty_pcs');
            }

            if (!Schema::hasColumn('penjualans', 'total_penjualan')) {
                $table->decimal('total_penjualan', 12, 2)->default(0)->after('harga_satuan');
            }

            if (!Schema::hasColumn('penjualans', 'jumlah_pcs')) {
                $table->integer('jumlah_pcs')->default(0)->after('harga_satuan');
            }

            if (!Schema::hasColumn('penjualans', 'tanggal_penjualan')) {
                $table->date('tanggal_penjualan')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'produk_siap_jual_id')) {
                $table->dropForeignKeyIfExists(['produk_siap_jual_id']);
                $table->dropColumn('produk_siap_jual_id');
            }

            if (Schema::hasColumn('penjualans', 'harga_satuan')) {
                $table->dropColumn('harga_satuan');
            }

            if (Schema::hasColumn('penjualans', 'total_penjualan')) {
                $table->dropColumn('total_penjualan');
            }

            if (Schema::hasColumn('penjualans', 'jumlah_pcs')) {
                $table->dropColumn('jumlah_pcs');
            }

            if (Schema::hasColumn('penjualans', 'tanggal_penjualan')) {
                $table->dropColumn('tanggal_penjualan');
            }
        });
    }
};
