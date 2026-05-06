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
        Schema::table('stock_gudang', function (Blueprint $table) {
            $table->integer('sisa_stock_pcs')
                ->default(0)
                ->after('jumlah_stock')
                ->comment('Sisa stok dalam PCS setelah pack terbagi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            $table->dropColumn('sisa_stock_pcs');
        });
    }
};
