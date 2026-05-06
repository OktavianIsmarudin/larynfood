<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggunaan_modal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('saldo_modal_id')->constrained('saldo_modal')->onDelete('cascade');
            $table->foreignId('pembelian_id')->nullable()->constrained('pembelians')->onDelete('set null');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->onDelete('set null');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('jenis')->default('pengeluaran'); // pengeluaran, pemasukan_kembali
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'saldo_modal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggunaan_modal');
    }
};
