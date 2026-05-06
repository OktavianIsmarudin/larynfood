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
        Schema::create('stock_gudang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onUpdate('cascade')->onDelete('set null');
            $table->string('nama_produk');
            $table->string('satuan_utama'); // pack, pcs, meter, dll
            $table->integer('konversi_satuan')->default(1); // 1 pack = 25 pcs
            $table->integer('jumlah_stock'); // dalam satuan utama
            $table->string('gudang_penyimpanan')->nullable();
            $table->string('sku')->unique(); // SKU unik per produk per user
            $table->string('gambar_produk')->nullable();
            $table->decimal('harga_beli', 12, 2)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('supplier_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_gudang');
    }
};
