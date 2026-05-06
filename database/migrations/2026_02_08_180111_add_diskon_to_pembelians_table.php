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
        Schema::table('pembelians', function (Blueprint $table) {
            $table->enum('tipe_diskon', ['persen', 'nominal'])->nullable()->after('harga_satuan');
            $table->decimal('diskon', 15, 2)->default(0)->after('tipe_diskon');
            $table->decimal('subtotal', 15, 2)->default(0)->after('diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn(['tipe_diskon', 'diskon', 'subtotal']);
        });
    }
};
