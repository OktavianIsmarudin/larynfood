<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists first
        if (Schema::hasTable('produk_siap_juals') && Schema::hasColumn('produk_siap_juals', 'pcs_per_paket')) {
            // Update semua produk_siap_juals yang pcs_per_paket kosong/null
            // Set ke default 1
            DB::table('produk_siap_juals')
                ->whereNull('pcs_per_paket')
                ->orWhere('pcs_per_paket', 0)
                ->update(['pcs_per_paket' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu di-reverse
    }
};
