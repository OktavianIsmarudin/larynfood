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
            // Add ongkir (shipping cost) if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'ongkir')) {
                $table->decimal('ongkir', 12, 2)->default(0)->nullable()->after('total_penjualan');
            }

            // Add keterangan (notes/description) if it doesn't exist
            if (!Schema::hasColumn('penjualans', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('alamat_pengiriman');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'ongkir')) {
                $table->dropColumn('ongkir');
            }
            if (Schema::hasColumn('penjualans', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
