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
            $table->integer('jumlah_pcs_jual')->default(1)->after('margin_laba');
            $table->decimal('hpp_paket', 12, 2)->default(0)->after('jumlah_pcs_jual');
            $table->decimal('modal_paket', 12, 2)->default(0)->after('hpp_paket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_siap_juals', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pcs_jual', 'hpp_paket', 'modal_paket']);
        });
    }
};
