<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix harga_satuan and recalculate totals using harga_jual_per_paket
        // Unit penjualan = PAKET, jadi harga_satuan harus = harga_jual_per_paket
        
        DB::statement(<<<SQL
            UPDATE penjualans p
            JOIN produk_siap_juals psj ON p.produk_siap_jual_id = psj.id
            SET 
                p.harga_satuan = COALESCE(psj.harga_jual_per_paket, psj.harga_jual, 0),
                p.total_penjualan = p.jumlah_pcs * COALESCE(psj.harga_jual_per_paket, psj.harga_jual, 0),
                p.hpp_total = p.jumlah_pcs * COALESCE(psj.hpp_per_pcs, 0),
                p.laba = p.jumlah_pcs * COALESCE(psj.harga_jual_per_paket, psj.harga_jual, 0) - (p.jumlah_pcs * COALESCE(psj.hpp_per_pcs, 0))
            WHERE p.total_penjualan > 0
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data fix, so down() doesn't need to do anything
        // (reverting would lose the correct data)
    }
};
