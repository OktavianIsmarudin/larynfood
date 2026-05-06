<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_gudang', 'energi_kkal')) {
                $table->decimal('energi_kkal', 10, 2)->nullable()->default(null)->after('status_stock');
            }
            if (!Schema::hasColumn('stock_gudang', 'protein_g')) {
                $table->decimal('protein_g', 10, 2)->nullable()->default(null)->after('energi_kkal');
            }
            if (!Schema::hasColumn('stock_gudang', 'lemak_g')) {
                $table->decimal('lemak_g', 10, 2)->nullable()->default(null)->after('protein_g');
            }
            if (!Schema::hasColumn('stock_gudang', 'karbohidrat_g')) {
                $table->decimal('karbohidrat_g', 10, 2)->nullable()->default(null)->after('lemak_g');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            $table->dropColumn(['energi_kkal', 'protein_g', 'lemak_g', 'karbohidrat_g']);
        });
    }
};
