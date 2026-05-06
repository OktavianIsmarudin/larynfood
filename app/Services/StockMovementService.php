<?php

namespace App\Services;

use App\Models\ProdukPaket;
use App\Models\StockGudang;
use App\Models\ProdukSiapJual;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Service untuk mengelola pergerakan stok antara:
 * - Stock Gudang (stok REAL)
 * - Produk Siap Jual (stok siap jual)
 * 
 * Setiap pergerakan dicatat dalam tabel stock_movements
 */
class StockMovementService
{
    /**
     * Catat pergerakan stok OUT: gudang -> siap jual
     * (saat user klik "Tambah Stock" di Produk Siap Jual)
     * 
     * @param StockGudang $stockGudang
     * @param ProdukSiapJual $produkSiapJual
     * @param int $pcs Jumlah PCS yang digerakkan
     * @param string|null $keterangan Keterangan/alasan
     * @return StockMovement
     */
    public function recordOutMovement(
        StockGudang $stockGudang,
        ProdukSiapJual $produkSiapJual,
        int $pcs,
        ?string $keterangan = null
    ): StockMovement {
        return StockMovement::create([
            'user_id' => Auth::id(),
            'stock_gudang_id' => $stockGudang->id,
            'produk_siap_jual_id' => $produkSiapJual->id,
            'type' => 'OUT',
            'pcs' => $pcs,
            'keterangan' => $keterangan ?? "Tambah stock: {$pcs} PCS",
            'reference_type' => 'tambah_stock',
            'reference_id' => $produkSiapJual->id,
        ]);
    }

    /**
     * Catat pergerakan stok IN: siap jual -> gudang
     * (saat user menghapus Produk Siap Jual atau retur)
     * 
     * @param StockGudang $stockGudang
     * @param ProdukSiapJual $produkSiapJual
     * @param int $pcs Jumlah PCS yang dikembalikan
     * @param string|null $referenceType Tipe referensi (delete_psj, retur, dll)
     * @param int|null $referenceId ID referensi
     * @return StockMovement
     */
    public function recordInMovement(
        StockGudang $stockGudang,
        ProdukSiapJual $produkSiapJual,
        int $pcs,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): StockMovement {
        return StockMovement::create([
            'user_id' => Auth::id(),
            'stock_gudang_id' => $stockGudang->id,
            'produk_siap_jual_id' => $produkSiapJual->id,
            'type' => 'IN',
            'pcs' => $pcs,
            'keterangan' => "Kembalikan {$pcs} PCS dari PSJ",
            'reference_type' => $referenceType ?? 'delete_psj',
            'reference_id' => $referenceId ?? $produkSiapJual->id,
        ]);
    }

    /**
     * Tambah stock siap jual dari stock gudang
     * Dengan transaction dan pencatatan movement
     * 
     * LOGIKA YANG DIUNCI:
     * 1. ❌ JANGAN ubah hpp_per_pcs
     * 2. ❌ JANGAN ubah harga_jual
     * 3. ✅ HANYA tambah stok_siap_jual (dalam satuan PAKET)
     * 4. ✅ KURANGI stock_gudang (HANYA untuk tipe single)
     * 5. ✅ Catat di stock_movements (HANYA untuk tipe single)
     * 
     * Flow stok SINGLE:
     * - Tambah Stock → gudang berkurang, PSJ bertambah
     * - Penjualan → PSJ berkurang
     * - Delete PSJ → gudang dikembalikan
     * 
     * Flow stok PAKET:
     * - Tambah Stock → HANYA PSJ bertambah (gudang TIDAK dikurangi)
     * - Penjualan → PSJ berkurang DAN gudang komponen berkurang
     * - Delete PSJ → HANYA hapus PSJ (gudang TIDAK dikembalikan)
     * 
     * @param ProdukSiapJual $produkSiapJual
     * @param int $jumlahPaket Jumlah PAKET (bukan PCS)
     * @return array Result dengan pesan sukses dan data updated
     * @throws Exception Jika validasi gagal
     */
    public function tambahStockSiapJual(
        ProdukSiapJual $produkSiapJual,
        int $jumlahPaket
    ): array {
        return DB::transaction(function () use ($produkSiapJual, $jumlahPaket) {
            // STEP 1: Validasi input
            if ($jumlahPaket <= 0) {
                throw new \Exception('Jumlah paket harus lebih dari 0');
            }

            // Ambil pcs_per_paket, HARUS ada
            $pcsPerPaket = $produkSiapJual->pcs_per_paket ?? 0;
            
            if ($pcsPerPaket <= 0) {
                throw new \Exception('Isi PCS per paket tidak valid. Silakan edit produk untuk mengatur nilai ini.');
            }

            // === EXTENSION: Handle paket type ===
            if ($produkSiapJual->isPaket()) {
                return $this->tambahStockPaket($produkSiapJual, $jumlahPaket, $pcsPerPaket);
            }
            
            // === EXISTING LOGIC: Handle single type ===
            return $this->tambahStockSingle($produkSiapJual, $jumlahPaket, $pcsPerPaket);
        });
    }

    /**
     * Tambah stock siap jual untuk tipe SINGLE
     * 
     * LOGIKA:
     * 1. Lock stock gudang untuk konsistensi
     * 2. Validasi stok cukup
     * 3. KURANGI stock gudang (bahan baku terpakai saat produksi)
     * 4. Tambah stok_siap_jual
     * 5. Catat stock movement
     */
    protected function tambahStockSingle(
        ProdukSiapJual $produkSiapJual,
        int $jumlahPaket,
        int $pcsPerPaket
    ): array {
        // STEP 1: Lock stock gudang untuk konsistensi data
        $stockGudang = StockGudang::lockForUpdate()->findOrFail($produkSiapJual->stock_gudang_id);

        // STEP 2: Hitung total PCS yang dibutuhkan
        $totalPcsNeeded = $jumlahPaket * $pcsPerPaket;
        $pcsSisaGudang = $stockGudang->pcs_sisa ?? 0;

        // STEP 3: Validasi - stock gudang harus cukup
        if ($pcsSisaGudang < $totalPcsNeeded) {
            throw new \Exception(
                "Stock gudang tidak cukup. Dibutuhkan: {$totalPcsNeeded} PCS, Tersedia: {$pcsSisaGudang} PCS"
            );
        }

        // STEP 4: KURANGI stock gudang (bahan baku terpakai saat produksi)
        $stockGudang->pcs_terpakai = ($stockGudang->pcs_terpakai ?? 0) + $totalPcsNeeded;
        $stockGudang->pcs_sisa = max(0, $pcsSisaGudang - $totalPcsNeeded);

        if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
            $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
        }

        $stockGudang->save();

        // STEP 5: Catat stock movement OUT
        $this->recordOutMovement($stockGudang, $produkSiapJual, $totalPcsNeeded);

        // STEP 6: TAMBAH stok_siap_jual (dalam satuan PAKET)
        $produkSiapJual->stok_siap_jual += $jumlahPaket;
        $produkSiapJual->save();

        return [
            'success' => true,
            'message' => "Berhasil tambah {$jumlahPaket} paket siap jual ({$totalPcsNeeded} PCS bahan baku terpakai)",
            'stok_siap_jual' => $produkSiapJual->stok_siap_jual,
            'pcs_sisa_gudang' => $stockGudang->pcs_sisa,
            'pcs_terpakai_gudang' => $stockGudang->pcs_terpakai,
            'info' => "Stock gudang dikurangi {$totalPcsNeeded} PCS",
        ];
    }

    /**
     * Tambah stock siap jual untuk tipe PAKET
     * 
     * LOGIKA:
     * - Tambah stok_siap_jual
     * - KURANGI stock gudang untuk SETIAP komponen paket
     * - Stock gudang komponen dikurangi berdasarkan qty_per_paket × jumlahPaket
     */
    protected function tambahStockPaket(
        ProdukSiapJual $produkSiapJual,
        int $jumlahPaket,
        int $pcsPerPaket
    ): array {
        // Pastikan produk paket ada
        $produkPaket = $produkSiapJual->produkPaket;
        
        if (!$produkPaket) {
            throw new \Exception('Produk paket tidak ditemukan');
        }

        // Load detail komponen paket
        $details = $produkPaket->details()->with('stockGudang')->get();

        if ($details->isEmpty()) {
            throw new \Exception("Paket '{$produkPaket->nama_paket}' tidak memiliki komponen.");
        }

        // STEP 1: Validasi stok cukup untuk SEMUA komponen
        foreach ($details as $detail) {
            $stockGudang = StockGudang::lockForUpdate()->findOrFail($detail->stock_gudang_id);
            $kebutuhan = (float) $detail->qty_per_paket * $jumlahPaket;
            $pcsSisa = (float) ($stockGudang->pcs_sisa ?? 0);

            if ($pcsSisa < $kebutuhan) {
                throw new \Exception(
                    "Stok gudang '{$stockGudang->nama_produk}' tidak cukup. " .
                    "Dibutuhkan: {$kebutuhan} PCS, Tersedia: {$pcsSisa} PCS"
                );
            }
        }

        // STEP 2: Kurangi stock gudang untuk SETIAP komponen
        foreach ($details as $detail) {
            $stockGudang = StockGudang::lockForUpdate()->findOrFail($detail->stock_gudang_id);
            $kebutuhan = (float) $detail->qty_per_paket * $jumlahPaket;

            $stockGudang->pcs_terpakai = ($stockGudang->pcs_terpakai ?? 0) + $kebutuhan;
            $stockGudang->pcs_sisa = max(0, ($stockGudang->pcs_sisa ?? 0) - $kebutuhan);

            if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
                $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
            }

            $stockGudang->save();

            // Catat stock movement OUT
            $this->recordOutMovement($stockGudang, $produkSiapJual, (int) ceil($kebutuhan));
        }

        // STEP 3: Tambah stok_siap_jual
        $produkSiapJual->stok_siap_jual += $jumlahPaket;
        $produkSiapJual->save();

        return [
            'success' => true,
            'message' => "Berhasil tambah {$jumlahPaket} paket siap jual. Stok gudang komponen telah dikurangi.",
            'stok_siap_jual' => $produkSiapJual->stok_siap_jual,
            'pcs_sisa_gudang' => null,
            'pcs_terpakai_gudang' => null,
            'info' => 'Stok gudang komponen telah dikurangi',
        ];
    }

    /**
     * Kembalikan stok saat Produk Siap Jual dihapus
     * 
     * LOGIKA:
     * - Single: Kembalikan stock gudang (pcs_terpakai - pcs, pcs_sisa + pcs)
     * - Paket: Kembalikan stock gudang setiap komponen via restoreStok()
     * - Catat stock movement IN
     * - Hapus PSJ
     * 
     * @param ProdukSiapJual $produkSiapJual
     * @return array Result dengan pesan sukses
     * @throws Exception Jika validasi gagal
     */
    public function restoreStockOnDelete(ProdukSiapJual $produkSiapJual): array
    {
        return DB::transaction(function () use ($produkSiapJual) {
            // STEP 1: Ambil data sebelum hapus
            $stokSiapJual = $produkSiapJual->stok_siap_jual ?? 0;
            $pcsPerPaket = $produkSiapJual->pcs_per_paket ?? 1;
            
            // === Handle PAKET type ===
            if ($produkSiapJual->isPaket()) {
                // Untuk paket: stok gudang PERLU dikembalikan
                // karena gudang SUDAH dikurangi saat Tambah Stock.
                if ($stokSiapJual > 0 && $produkSiapJual->produk_paket_id) {
                    $produkPaket = ProdukPaket::with('details.stockGudang')->find($produkSiapJual->produk_paket_id);
                    if ($produkPaket) {
                        foreach ($produkPaket->details as $detail) {
                            $stockGudang = StockGudang::lockForUpdate()->find($detail->stock_gudang_id);
                            if ($stockGudang) {
                                $pcsToRestore = (float) $detail->qty_per_paket * $stokSiapJual;
                                $stockGudang->pcs_terpakai = max(0, ($stockGudang->pcs_terpakai ?? 0) - $pcsToRestore);
                                $stockGudang->pcs_sisa = ($stockGudang->pcs_sisa ?? 0) + $pcsToRestore;

                                if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
                                    $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
                                }

                                $stockGudang->save();
                            }
                        }
                    }
                }

                $produkSiapJual->delete();
                
                return [
                    'success' => true,
                    'message' => "Produk paket siap jual berhasil dihapus. Stok gudang komponen telah dikembalikan.",
                    'stok_siap_jual_dihapus' => $stokSiapJual,
                ];
            }
            
            // === Handle SINGLE type ===
            if ($stokSiapJual > 0 && $produkSiapJual->stock_gudang_id) {
                $stockGudang = StockGudang::lockForUpdate()
                    ->findOrFail($produkSiapJual->stock_gudang_id);
                
                $totalPcsToRestore = $stokSiapJual * $pcsPerPaket;
                
                // Restore stock gudang
                $stockGudang->pcs_terpakai = max(0, ($stockGudang->pcs_terpakai ?? 0) - $totalPcsToRestore);
                $stockGudang->pcs_sisa = ($stockGudang->pcs_sisa ?? 0) + $totalPcsToRestore;
                
                if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
                    $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
                }
                
                $stockGudang->save();
                
                // Catat stock movement IN
                $this->recordInMovement(
                    $stockGudang,
                    $produkSiapJual,
                    $totalPcsToRestore,
                    'delete_psj',
                    $produkSiapJual->id
                );
            }
            
            $produkSiapJual->delete();

            return [
                'success' => true,
                'message' => "Produk siap jual berhasil dihapus. Stok gudang telah dikembalikan.",
                'stok_siap_jual_dihapus' => $stokSiapJual,
            ];
        });
    }

    /**
     * Get movement history untuk stock gudang
     */
    public function getMovementHistory(StockGudang $stockGudang, int $limit = 50)
    {
        return StockMovement::where('stock_gudang_id', $stockGudang->id)
            ->with('produkSiapJual')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get movement history untuk produk siap jual
     */
    public function getProdukMovementHistory(ProdukSiapJual $produkSiapJual, int $limit = 50)
    {
        return StockMovement::where('produk_siap_jual_id', $produkSiapJual->id)
            ->with('stockGudang')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Hitung total PCS OUT untuk stock gudang
     */
    public function getTotalPcsOut(StockGudang $stockGudang): int
    {
        return StockMovement::where('stock_gudang_id', $stockGudang->id)
            ->where('type', 'OUT')
            ->sum('pcs');
    }

    /**
     * Hitung total PCS IN untuk stock gudang
     */
    public function getTotalPcsIn(StockGudang $stockGudang): int
    {
        return StockMovement::where('stock_gudang_id', $stockGudang->id)
            ->where('type', 'IN')
            ->sum('pcs');
    }

    /**
     * Catat penggunaan peralatan/kemasan
     * 
     * @param StockGudang $stockGudang Peralatan yang digunakan
     * @param ProdukSiapJual $produkSiapJual Produk yang menggunakan peralatan
     * @param int $pcs Jumlah peralatan (PCS)
     * @param string|null $keterangan Keterangan penggunaan
     * @return StockMovement
     */
    public function recordEquipmentUsage(
        StockGudang $stockGudang,
        ProdukSiapJual $produkSiapJual,
        int $pcs,
        ?string $keterangan = null
    ): StockMovement {
        return StockMovement::create([
            'user_id' => Auth::id(),
            'stock_gudang_id' => $stockGudang->id,
            'produk_siap_jual_id' => $produkSiapJual->id,
            'type' => 'OUT',
            'pcs' => $pcs,
            'keterangan' => $keterangan ?? "Pemakaian peralatan: {$pcs} PCS",
            'reference_type' => 'pemakaian_peralatan',
            'reference_id' => $produkSiapJual->id,
        ]);
    }
}

