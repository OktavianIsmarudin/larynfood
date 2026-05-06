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
        Schema::table('penjualans', function (Blueprint $table) {
            // Add customer_id with nullable foreign key
            if (!Schema::hasColumn('penjualans', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('customers')
                    ->nullOnDelete()
                    ->after('user_id');
            }

            // Add nama_customer_snapshot to preserve customer name at time of sale
            if (!Schema::hasColumn('penjualans', 'nama_customer_snapshot')) {
                $table->string('nama_customer_snapshot')->nullable()->after('customer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'customer_id')) {
                $table->dropForeignKey(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('penjualans', 'nama_customer_snapshot')) {
                $table->dropColumn('nama_customer_snapshot');
            }
        });
    }
};
