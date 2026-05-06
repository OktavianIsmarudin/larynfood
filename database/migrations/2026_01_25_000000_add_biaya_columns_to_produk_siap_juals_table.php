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
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            // Biaya-biaya lain-lain
            $table->decimal('biaya_packing', 12, 2)->default(0)->after('margin_laba');
            $table->decimal('biaya_saos', 12, 2)->default(0)->after('biaya_packing');
            $table->decimal('biaya_sumpit', 12, 2)->default(0)->after('biaya_saos');
            $table->decimal('biaya_tenaga', 12, 2)->default(0)->after('biaya_sumpit');
            
            // Calculated fields
            $table->decimal('total_biaya_lain', 12, 2)->default(0)->after('biaya_tenaga');
            $table->decimal('hpp_total_per_pcs', 12, 2)->default(0)->after('total_biaya_lain');
            $table->decimal('harga_jual_per_pcs', 12, 2)->default(0)->after('hpp_total_per_pcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            $table->dropColumn([
                'biaya_packing',
                'biaya_saos',
                'biaya_sumpit',
                'biaya_tenaga',
                'total_biaya_lain',
                'hpp_total_per_pcs',
                'harga_jual_per_pcs',
            ]);
        });
    }
};
