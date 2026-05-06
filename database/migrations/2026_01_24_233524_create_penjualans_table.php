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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('produk_id')->nullable()->constrained('produk_siap_juals')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('payment_methods')->onUpdate('cascade')->onDelete('set null');
            $table->integer('qty_pcs');
            $table->decimal('harga_jual', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0); // nominal atau persen
            $table->string('tipe_diskon')->default('nominal'); // nominal or persen
            $table->decimal('promo', 12, 2)->default(0);
            $table->decimal('pajak', 12, 2)->default(0);
            $table->text('alamat_pengiriman')->nullable();
            $table->string('metode_pengiriman')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->decimal('total_bayar', 12, 2);
            $table->decimal('laba', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['lunas', 'utang', 'dp'])->default('lunas');
            $table->timestamps();

            $table->index('user_id');
            $table->index('customer_id');
            $table->index('produk_id');
            $table->index('metode_pembayaran_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
