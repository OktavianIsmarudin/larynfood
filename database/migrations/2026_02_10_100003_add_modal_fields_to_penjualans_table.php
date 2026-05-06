<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'modal_terpakai')) {
                $table->decimal('modal_terpakai', 15, 2)->nullable()->after('laba');
            }
            if (!Schema::hasColumn('penjualans', 'keterangan_modal')) {
                $table->string('keterangan_modal')->nullable()->after('modal_terpakai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'modal_terpakai')) {
                $table->dropColumn('modal_terpakai');
            }
            if (Schema::hasColumn('penjualans', 'keterangan_modal')) {
                $table->dropColumn('keterangan_modal');
            }
        });
    }
};
