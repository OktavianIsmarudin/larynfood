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
        Schema::table('customers', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('customers', 'kontak')) {
                $table->string('kontak')->nullable()->after('nama_customer');
            }
            if (!Schema::hasColumn('customers', 'telepon')) {
                $table->string('telepon')->nullable()->after('kontak');
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('telepon');
            }
            if (!Schema::hasColumn('customers', 'alamat')) {
                $table->text('alamat')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'kota')) {
                $table->string('kota')->nullable()->after('alamat');
            }
        });

        // Add unique constraint (composite unique on user_id + nama_customer)
        // We'll skip dropping old unique to avoid errors
        Schema::table('customers', function (Blueprint $table) {
            try {
                $table->unique(['user_id', 'nama_customer'], 'unique_customer_per_user');
            } catch (\Exception $e) {
                // Constraint already exists, that's okay
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'kontak')) {
                $table->dropColumn('kontak');
            }
            if (Schema::hasColumn('customers', 'telepon')) {
                $table->dropColumn('telepon');
            }
            if (Schema::hasColumn('customers', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('customers', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('customers', 'kota')) {
                $table->dropColumn('kota');
            }

            // Drop composite unique
            try {
                $table->dropUnique('unique_customer_per_user');
            } catch (\Exception $e) {
                // Constraint doesn't exist
            }
        });
    }
};
