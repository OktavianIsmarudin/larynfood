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
            $table->integer('stock_ready')->default(0)->after('stok_pcs')->comment('Stock produk siap jual yang tersedia untuk dijual (dalam PCS)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            $table->dropColumn('stock_ready');
        });
    }
};
