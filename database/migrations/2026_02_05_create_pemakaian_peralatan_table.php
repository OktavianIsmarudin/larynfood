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
        Schema::create('pemakaian_peralatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('produk_siap_jual_id')->constrained('produk_siap_juals')->onDelete('cascade');
            $table->foreignId('stock_gudang_id')->constrained('stock_gudang')->onDelete('cascade');
            $table->integer('jumlah_pakai')->comment('Jumlah peralatan/kemasan yang dipakai (PCS)');
            $table->text('keterangan')->nullable()->comment('Catatan tambahan tentang penggunaan');
            $table->timestamps();

            $table->index(['produk_siap_jual_id', 'user_id']);
            $table->index(['stock_gudang_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_peralatan');
    }
};
