<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel produk_paket_details
 * 
 * TUJUAN:
 * Menyimpan detail item-item yang menyusun sebuah paket/platter.
 * Many-to-many relationship antara produk_pakets dan stock_gudang
 * dengan tambahan qty_per_paket.
 * 
 * CONTOH DATA:
 * produk_paket_id | stock_gudang_id | qty_per_paket | satuan
 * 1 (Paket A)     | 5 (Nasi)        | 200           | gram
 * 1 (Paket A)     | 8 (Ayam)        | 1             | pcs  
 * 1 (Paket A)     | 12 (Sambal)     | 1             | pcs
 * 
 * LOGIKA STOK:
 * - qty_per_paket dalam satuan PCS (konversi sudah dihitung)
 * - Saat jual 1 paket → kurangi semua item sesuai qty masing-masing
 * - Saat jual 5 paket → qty dikali 5 untuk setiap item
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_paket_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_paket_id')
                ->constrained('produk_pakets')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('stock_gudang_id')
                ->constrained('stock_gudang')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->decimal('qty_per_paket', 10, 2); // Jumlah item per 1 paket (dalam PCS)
            $table->string('keterangan')->nullable(); // Catatan tambahan per item
            $table->timestamps();

            // Unique constraint: 1 item hanya bisa muncul 1x per paket
            $table->unique(['produk_paket_id', 'stock_gudang_id'], 'paket_item_unique');
            
            $table->index('produk_paket_id');
            $table->index('stock_gudang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_paket_details');
    }
};
