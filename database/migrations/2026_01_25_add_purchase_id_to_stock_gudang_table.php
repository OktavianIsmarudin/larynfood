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
            $table->foreignId('purchase_id')
                ->nullable()
                ->after('user_id')
                ->constrained('pembelians')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplicate stock dari pembelian yang sama
            $table->unique('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            $table->dropUnique(['purchase_id']);
            $table->dropForeignKeyIfExists(['purchase_id']);
            $table->dropColumn('purchase_id');
        });
    }
};
