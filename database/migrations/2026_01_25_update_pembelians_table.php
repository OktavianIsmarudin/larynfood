<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            // Add category_id if not exists
            if (!Schema::hasColumn('pembelians', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->onUpdate('cascade')->onDelete('set null')->after('supplier_id');
            }
            
            // Add status_stock if not exists
            if (!Schema::hasColumn('pembelians', 'status_stock')) {
                $table->enum('status_stock', ['belum_masuk_gudang', 'sudah_masuk_gudang'])->default('belum_masuk_gudang')->after('bukti_pembelian');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            if (Schema::hasColumn('pembelians', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            
            if (Schema::hasColumn('pembelians', 'status_stock')) {
                $table->dropColumn('status_stock');
            }
        });
    }
};
