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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onUpdate('cascade')->onDelete('set null');
            $table->string('nama_produk');
            $table->integer('qty');
            $table->decimal('total_biaya_awal', 12, 2);
            $table->decimal('total_pengeluaran', 12, 2);
            $table->date('tanggal_pembelian');
            $table->string('bukti_pembelian')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('supplier_id');
            $table->index('tanggal_pembelian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
