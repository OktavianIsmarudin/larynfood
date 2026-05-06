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
        Schema::table('categories', function (Blueprint $table) {
            // Add jenis_kategori column if it doesn't exist
            if (!Schema::hasColumn('categories', 'jenis_kategori')) {
                $table->enum('jenis_kategori', ['produk', 'peralatan'])
                    ->default('produk')
                    ->after('nama_kategori')
                    ->comment('produk: Produk/Bahan untuk penjualan | peralatan: Kemasan/Peralatan inventory');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'jenis_kategori')) {
                $table->dropColumn('jenis_kategori');
            }
        });
    }
};
