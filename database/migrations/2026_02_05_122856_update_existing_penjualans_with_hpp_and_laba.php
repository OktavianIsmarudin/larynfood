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
        // Update hpp_total and laba for all existing penjualan records
        // hpp_total = jumlah_pcs * hpp_per_pcs (from produk_siap_jual)
        // laba = total_penjualan - hpp_total
        
        DB::statement(<<<SQL
            UPDATE penjualans p
            JOIN produk_siap_juals psj ON p.produk_siap_jual_id = psj.id
            SET 
                p.hpp_total = p.jumlah_pcs * COALESCE(psj.hpp_per_pcs, 0),
                p.laba = p.total_penjualan - (p.jumlah_pcs * COALESCE(psj.hpp_per_pcs, 0))
            WHERE p.hpp_total = 0
            AND p.total_penjualan > 0
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset hpp_total and laba to 0
        DB::statement(<<<SQL
            UPDATE penjualans
            SET hpp_total = 0, laba = 0
        SQL);
    }
};
