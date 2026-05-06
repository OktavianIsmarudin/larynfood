<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_method')) {
                $table->enum('delivery_method', ['pickup', 'delivery'])->default('pickup')->after('shipping_address');
            }

            if (!Schema::hasColumn('orders', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('payment_type');
            }

            if (!Schema::hasColumn('orders', 'tracking_note')) {
                $table->text('tracking_note')->nullable()->after('notes');
            }
        });

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_user_id_foreign');
            DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE orders MODIFY customer_email VARCHAR(255) NULL');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_user_id_foreign');
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL');
            DB::statement('ALTER TABLE orders ALTER COLUMN customer_email DROP NOT NULL');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_user_id_foreign');
            DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE orders MODIFY customer_email VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_user_id_foreign');
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id SET NOT NULL');
            DB::statement('ALTER TABLE orders ALTER COLUMN customer_email SET NOT NULL');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_method')) {
                $table->dropColumn('delivery_method');
            }

            if (Schema::hasColumn('orders', 'payment_proof_path')) {
                $table->dropColumn('payment_proof_path');
            }

            if (Schema::hasColumn('orders', 'tracking_note')) {
                $table->dropColumn('tracking_note');
            }
        });
    }
};
