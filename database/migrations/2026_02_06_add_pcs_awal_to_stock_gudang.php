<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * LOGIKA PCS AWAL:
     * - pcs_awal = Total akumulasi barang MASUK dari pembelian (TIDAK BERKURANG)
     * - Hanya bertambah saat ada pembelian baru
     * - pcs_terpakai = Barang yang sudah dipakai/dijual
     * - pcs_sisa = pcs_awal - pcs_terpakai (AUTO CALCULATED)
     * 
     * Migration ini:
     * 1. Tambah column pcs_awal
     * 2. Fill existing records: pcs_awal = total_pcs
     * 3. Remove total_pcs (deprecated, ganti dengan pcs_awal)
     */
    public function up(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            // Add pcs_awal column SEBELUM pcs_terpakai
            if (!Schema::hasColumn('stock_gudang', 'pcs_awal')) {
                $table->integer('pcs_awal')->default(0)->after('konversi_satuan');
            }
        });
        
        // Fill existing records
        // pcs_awal = total_pcs jika total_pcs ada, atau (jumlah_pack * konversi_satuan)
        DB::statement('
            UPDATE stock_gudang 
            SET pcs_awal = COALESCE(total_pcs, COALESCE(jumlah_pack, 0) * COALESCE(konversi_satuan, 1))
            WHERE pcs_awal = 0
        ');
        
        // Recalculate pcs_sisa = pcs_awal - pcs_terpakai untuk semua record
        DB::statement('
            UPDATE stock_gudang 
            SET pcs_sisa = pcs_awal - COALESCE(pcs_terpakai, 0)
        ');
    }

    public function down(): void
    {
        Schema::table('stock_gudang', function (Blueprint $table) {
            if (Schema::hasColumn('stock_gudang', 'pcs_awal')) {
                $table->dropColumn('pcs_awal');
            }
        });
    }
};
