<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan produk_paket_id ke produk_siap_juals
 * 
 * LOGIKA BARU:
 * - ProdukSiapJual bisa berupa:
 *   1. Single Item → stock_gudang_id (existing)
 *   2. Paket/Platter → produk_paket_id (NEW)
 * 
 * ATURAN:
 * - stock_gudang_id dan produk_paket_id bersifat mutual exclusive
 * - Jika produk_paket_id diisi, maka stock_gudang_id harus null (atau diabaikan)
 * - Jika stock_gudang_id diisi, maka produk_paket_id harus null
 * 
 * SAAT PENJUALAN:
 * - Produk single → kurangi stok_siap_jual saja (existing logic)
 * - Produk paket → kurangi stok SEMUA item dalam paket details
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            // Tambah kolom produk_paket_id (nullable)
            $table->foreignId('produk_paket_id')
                ->nullable()
                ->after('stock_gudang_id')
                ->constrained('produk_pakets')
                ->onUpdate('cascade')
                ->onDelete('set null');
            
            // Flag untuk menentukan tipe produk
            $table->enum('tipe_produk', ['single', 'paket'])->default('single')->after('produk_paket_id');
            
            $table->index('produk_paket_id');
            $table->index('tipe_produk');
        });
        
        // Ubah stock_gudang_id menjadi nullable (karena paket tidak butuh)
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            $table->foreignId('stock_gudang_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            $table->dropForeign(['produk_paket_id']);
            $table->dropColumn(['produk_paket_id', 'tipe_produk']);
        });
    }
};
