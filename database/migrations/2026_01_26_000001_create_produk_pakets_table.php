<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel produk_pakets
 * 
 * TUJUAN:
 * Menyimpan data "resep/komposisi" paket/platter yang terdiri dari
 * beberapa item dari stock gudang. Contoh:
 * - "Paket Nasi Box A" = Nasi 200gr + Ayam Goreng 1pc + Sambal 1pc + Kerupuk 2pc
 * - "Platter Party 20 orang" = Nasi 5kg + Ayam 10pc + Sayur 2kg
 * 
 * LOGIKA:
 * - Produk Paket TIDAK langsung dijual, tapi menjadi "template/resep"
 * - Produk Siap Jual bisa reference ke Produk Paket
 * - Saat penjualan, stok SEMUA item dalam paket akan dikurangi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_pakets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_paket');
            $table->string('kode_paket')->nullable(); // SKU/kode unik paket
            $table->text('deskripsi')->nullable();
            $table->decimal('hpp_total', 15, 2)->default(0); // Dihitung otomatis dari sum detail
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'kode_paket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_pakets');
    }
};
