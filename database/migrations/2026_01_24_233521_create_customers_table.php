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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_customer');
            $table->string('kontak')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('email')->nullable();
            $table->integer('total_transaksi')->default(0);
            $table->enum('status_piutang', ['lunas', 'belum_lunas'])->default('lunas');
            $table->string('loyalty_card')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
