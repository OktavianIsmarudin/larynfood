<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk mencatat setiap pergerakan stok antara:
     * - Stock Gudang (stok REAL)
     * - Produk Siap Jual (stok siap jual)
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stock_gudang_id')->constrained('stock_gudang')->onDelete('cascade');
            $table->foreignId('produk_siap_jual_id')->nullable()->constrained('produk_siap_juals')->onDelete('set null');
            
            // Type: OUT (gudang -> siap jual), IN (siap jual -> gudang/retur), ADJUSTMENT
            $table->enum('type', ['OUT', 'IN', 'ADJUSTMENT'])->default('OUT');
            
            // Jumlah PCS yang digerakkan
            $table->integer('pcs');
            
            // Keterangan/alasan pergerakan stok
            $table->string('keterangan')->nullable();
            
            // Reference ke dokumen lain jika ada
            $table->string('reference_type')->nullable(); // 'tambah_stock', 'delete_psj', 'penjualan', dll
            $table->integer('reference_id')->nullable();
            
            // Field untuk audit trail
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk query cepat
            $table->index(['user_id', 'created_at']);
            $table->index(['stock_gudang_id', 'type']);
            $table->index(['produk_siap_jual_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
