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
            // Tambah field hpp_total jika belum ada
            if (!Schema::hasColumn('penjualans', 'hpp_total')) {
                $table->decimal('hpp_total', 12, 2)->default(0)->after('total_penjualan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'hpp_total')) {
                $table->dropColumn('hpp_total');
            }
        });
    }
};
