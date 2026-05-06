<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Refactor produk_siap_jual table dengan logika BARU:
     * - stok_siap_jual (stok yang ready untuk dijual, default 0)
     * - pcs_per_paket (berapa PCS dalam 1 paket)
     * 
     * Logika:
     * - Input HPP: TIDAK kurangi stock gudang
     * - Tambah Stock: KURANGI gudang, TAMBAH stok_siap_jual
     */
    public function up(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            // Tambah stok_siap_jual (stok yang tersedia untuk dijual)
            if (!Schema::hasColumn('produk_siap_juals', 'stok_siap_jual')) {
                $table->integer('stok_siap_jual')->default(0)->after('stok_pcs')->comment('Stok siap jual dalam satuan PAKET');
            }

            // Tambah pcs_per_paket (jumlah PCS per paket)
            if (!Schema::hasColumn('produk_siap_juals', 'pcs_per_paket')) {
                $table->integer('pcs_per_paket')->nullable()->after('stok_siap_jual')->comment('Jumlah PCS dalam 1 paket (contoh: 3 pcs)');
            }

            // Ubah field untuk konsistensi (harga_jual bisa berubah jadi harga_jual_per_paket)
            if (!Schema::hasColumn('produk_siap_juals', 'harga_jual_per_paket')) {
                $table->decimal('harga_jual_per_paket', 12, 2)->nullable()->after('harga_jual')->comment('Harga jual per paket (pcs_per_paket × harga per pcs)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            if (Schema::hasColumn('produk_siap_juals', 'stok_siap_jual')) {
                $table->dropColumn('stok_siap_jual');
            }
            if (Schema::hasColumn('produk_siap_juals', 'pcs_per_paket')) {
                $table->dropColumn('pcs_per_paket');
            }
            if (Schema::hasColumn('produk_siap_juals', 'harga_jual_per_paket')) {
                $table->dropColumn('harga_jual_per_paket');
            }
        });
    }
};
