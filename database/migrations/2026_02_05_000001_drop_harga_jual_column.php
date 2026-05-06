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
            // Simply drop harga_jual column if it exists
            if (Schema::hasColumn('penjualans', 'harga_jual')) {
                $table->dropColumn('harga_jual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'harga_jual')) {
                $table->decimal('harga_jual', 12, 2)->nullable();
            }
        });
    }
};
