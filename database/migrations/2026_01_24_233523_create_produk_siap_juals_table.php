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
        Schema::create('produk_siap_juals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stock_gudang_id')->constrained('stock_gudang')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_produk');
            $table->decimal('hpp_per_pcs', 12, 2); // Harga Pokok Penjualan per pcs
            $table->decimal('harga_jual', 12, 2);
            $table->decimal('margin_laba', 12, 2)->default(0);
            $table->integer('stok_pcs'); // Stok dalam satuan PCS
            $table->string('gambar_produk')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('stock_gudang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_siap_juals');
    }
};
