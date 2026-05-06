<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saldo_modal', function (Blueprint $table) {
            $table->foreignId('piutang_manual_id')->nullable()->after('sumber_modal')
                  ->constrained('piutang_manual')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('saldo_modal', function (Blueprint $table) {
            $table->dropForeign(['piutang_manual_id']);
            $table->dropColumn('piutang_manual_id');
        });
    }
};
