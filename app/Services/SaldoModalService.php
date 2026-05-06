<?php

namespace App\Services;

use App\Models\SaldoModal;
use App\Models\PenggunaanModal;
use App\Models\PiutangManual;
use Illuminate\Support\Facades\DB;

class SaldoModalService
{
    /**
     * Get overview data for saldo modal dashboard
     */
    public function getOverview(?int $userId = null): array
    {
        // If userId is null, aggregate from all users (untuk super admin)
        if ($userId === null) {
            $totalSaldoAwal = (float) SaldoModal::sum('saldo_awal');
            $totalPenggunaan = (float) PenggunaanModal::where('jenis', 'pengeluaran')->sum('nominal');
            $totalPemasukanKembali = (float) PenggunaanModal::where('jenis', 'pemasukan_kembali')->sum('nominal');
            $totalPiutangAktif = (float) PiutangManual::where('jenis', 'piutang')->where('status', 'belum_lunas')->sum('nominal');
            $totalHutangAktif = (float) PiutangManual::where('jenis', 'hutang')->where('status', 'belum_lunas')->sum('nominal');
        } else {
            $totalSaldoAwal = (float) SaldoModal::forUser($userId)->sum('saldo_awal');
            $totalPenggunaan = (float) PenggunaanModal::forUser($userId)->pengeluaran()->sum('nominal');
            $totalPemasukanKembali = (float) PenggunaanModal::forUser($userId)->pemasukanKembali()->sum('nominal');
            $totalPiutangAktif = (float) PiutangManual::forUser($userId)->piutang()->belumLunas()->sum('nominal');
            $totalHutangAktif = (float) PiutangManual::forUser($userId)->hutang()->belumLunas()->sum('nominal');
        }

        // Saldo akhir = total_saldo_awal - total_penggunaan + total_pemasukan_kembali
        $saldoAkhir = $totalSaldoAwal - $totalPenggunaan + $totalPemasukanKembali;

        return [
            'total_saldo_awal' => $totalSaldoAwal,
            'total_penggunaan' => $totalPenggunaan,
            'total_pemasukan_kembali' => $totalPemasukanKembali,
            'saldo_akhir' => $saldoAkhir,
            'is_negative' => $saldoAkhir < 0,
            'total_piutang_aktif' => $totalPiutangAktif,
            'total_hutang_aktif' => $totalHutangAktif,
        ];
    }

    /**
     * Create a new saldo modal entry
     */
    public function createSaldoModal(array $data): SaldoModal
    {
        return DB::transaction(function () use ($data) {
            $piutangManualId = null;

            // If sumber is "Pinjaman" and linked to existing hutang
            if (($data['sumber_modal'] ?? '') === 'Pinjaman' && !empty($data['piutang_manual_id'])) {
                $piutangManualId = $data['piutang_manual_id'];
            }
            // If sumber is "Pinjaman" and creating new hutang record
            elseif (($data['sumber_modal'] ?? '') === 'Pinjaman' && !empty($data['nama_pihak_pinjaman'])) {
                $piutang = PiutangManual::create([
                    'user_id' => auth()->id(),
                    'nama_pihak' => $data['nama_pihak_pinjaman'],
                    'jenis' => 'hutang',
                    'nominal' => $data['saldo_awal'],
                    'tanggal' => $data['tanggal'],
                    'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'] ?? null,
                    'keterangan' => 'Modal pinjaman - ' . ($data['keterangan'] ?? 'Saldo Modal'),
                    'status' => 'belum_lunas',
                ]);
                $piutangManualId = $piutang->id;
            }

            return SaldoModal::create([
                'user_id' => auth()->id(),
                'tanggal' => $data['tanggal'],
                'saldo_awal' => $data['saldo_awal'],
                'sumber_modal' => $data['sumber_modal'] ?? null,
                'piutang_manual_id' => $piutangManualId,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        });
    }

    /**
     * Create a penggunaan modal entry
     */
    public function createPenggunaan(array $data): PenggunaanModal
    {
        return DB::transaction(function () use ($data) {
            $penggunaan = PenggunaanModal::create([
                'user_id' => auth()->id(),
                'saldo_modal_id' => $data['saldo_modal_id'],
                'pembelian_id' => $data['pembelian_id'] ?? null,
                'penjualan_id' => $data['penjualan_id'] ?? null,
                'nominal' => $data['nominal'],
                'jenis' => $data['jenis'] ?? 'pengeluaran',
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            return $penggunaan;
        });
    }

    /**
     * Check if adding a penggunaan would cause negative saldo
     */
    public function wouldCauseNegative(int $saldoModalId, float $nominal): bool
    {
        $saldoModal = SaldoModal::with('penggunaanModal')->find($saldoModalId);
        if (!$saldoModal) return true;

        return ($saldoModal->saldo_akhir - $nominal) < 0;
    }

    /**
     * Get remaining saldo for a specific saldo modal
     */
    public function getRemainingSaldo(int $saldoModalId): float
    {
        $saldoModal = SaldoModal::with('penggunaanModal')->find($saldoModalId);
        if (!$saldoModal) return 0;

        return $saldoModal->saldo_akhir;
    }
}
