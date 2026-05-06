<?php

namespace App\Services;

use App\Models\StockGudang;
use Exception;

/**
 * Service untuk menangani pengurangan stok gudang berbasis PCS
 * dengan konversi satuan.
 * 
 * Logika:
 * - Stock disimpan dalam satuan besar (pack) + sisa dalam PCS
 * - Penjualan dilakukan dalam PCS
 * - Konversi satuan adalah tetap (misalnya 1 pack = 25 pcs)
 */
class StockReductionService
{
    /**
     * Validasi dan kurangi stok berdasarkan PCS yang dijual
     * 
     * Proses:
     * 1. Hitung total PCS = (jumlah_stock * konversi_satuan) + sisa_stock_pcs
     * 2. Validasi apakah stok cukup
     * 3. Hitung sisa PCS = total_pcs - pcs_dijual
     * 4. Update stok:
     *    - jumlah_stock = floor(sisa_pcs / konversi_satuan)
     *    - sisa_stock_pcs = sisa_pcs % konversi_satuan
     * 
     * @param StockGudang $stock
     * @param int $pcsToSell
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function reduce(StockGudang $stock, int $pcsToSell): array
    {
        // 1. Hitung total PCS tersedia
        $totalAvailablePcs = $stock->getTotalPcs();
        
        // 2. Validasi stok cukup
        if ($pcsToSell > $totalAvailablePcs) {
            return [
                'success' => false,
                'message' => sprintf(
                    "Stok tidak cukup. Tersedia: %d PCS (%d pack + %d pcs), Diminta: %d PCS",
                    $totalAvailablePcs,
                    $stock->jumlah_stock,
                    $stock->sisa_stock_pcs,
                    $pcsToSell
                ),
                'data' => [
                    'totalAvailable' => $totalAvailablePcs,
                    'requested' => $pcsToSell,
                    'shortfall' => $pcsToSell - $totalAvailablePcs,
                ]
            ];
        }

        // 3. Hitung sisa PCS setelah penjualan
        $remainingPcs = $totalAvailablePcs - $pcsToSell;

        // 4. Update stok (jangan pernah mengurangi dalam bentuk pecahan pack)
        $newJumlahStock = intdiv($remainingPcs, $stock->konversi_satuan);
        $newSisaStockPcs = $remainingPcs % $stock->konversi_satuan;

        try {
            $stock->update([
                'jumlah_stock' => $newJumlahStock,
                'sisa_stock_pcs' => $newSisaStockPcs,
                // Juga update pcs_terpakai & pcs_sisa agar sinkron
                'pcs_terpakai' => ($stock->pcs_terpakai ?? 0) + $pcsToSell,
                'pcs_sisa' => max(0, ($stock->pcs_sisa ?? 0) - $pcsToSell),
            ]);

            return [
                'data' => [
                    'pcsReduced' => $pcsToSell,
                    'previousTotal' => $totalAvailablePcs,
                    'currentTotal' => $stock->getTotalPcs(),
                    'currentStock' => [
                        'jumlah_pack' => $newJumlahStock,
                        'sisa_pcs' => $newSisaStockPcs,
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Gagal mengurangi stok: {$e->getMessage()}",
                'data' => []
            ];
        }
    }

    /**
     * Restore stok (menambah kembali) ketika penjualan dibatalkan
     * 
     * @param StockGudang $stock
     * @param int $pcsToRestore
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function restore(StockGudang $stock, int $pcsToRestore): array
    {
        $currentTotalPcs = $stock->getTotalPcs();
        $newTotalPcs = $currentTotalPcs + $pcsToRestore;

        $newJumlahStock = intdiv($newTotalPcs, $stock->konversi_satuan);
        $newSisaStockPcs = $newTotalPcs % $stock->konversi_satuan;

        try {
            $stock->update([
                'jumlah_stock' => $newJumlahStock,
                'sisa_stock_pcs' => $newSisaStockPcs,
                // Juga update pcs_terpakai & pcs_sisa agar sinkron
                'pcs_terpakai' => max(0, ($stock->pcs_terpakai ?? 0) - $pcsToRestore),
                'pcs_sisa' => ($stock->pcs_sisa ?? 0) + $pcsToRestore,
            ]);

            return [
                'success' => true,
                'message' => "Stok berhasil di-restore sebesar {$pcsToRestore} PCS",
                'data' => [
                    'pcsRestored' => $pcsToRestore,
                    'previousTotal' => $currentTotalPcs,
                    'currentTotal' => $stock->getTotalPcs(),
                    'currentStock' => [
                        'jumlah_pack' => $newJumlahStock,
                        'sisa_pcs' => $newSisaStockPcs,
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Gagal me-restore stok: {$e->getMessage()}",
                'data' => []
            ];
        }
    }

    /**
     * Hitung HPP per PCS
     * 
     * @param StockGudang $stock
     * @return float
     */
    public function getHppPerPcs(StockGudang $stock): float
    {
        return $stock->getHppPerPcs();
    }

    /**
     * Format tampilan stok dalam format "X pack + Y pcs"
     * 
     * @param StockGudang $stock
     * @return string
     */
    public function getDisplayStock(StockGudang $stock): string
    {
        return $stock->getDisplayStockAttribute();
    }
}
