<?php

namespace App\Services;

use App\Models\ProdukSiapJual;
use App\Models\StockGudang;
use App\Models\ProdukPaket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

/**
 * Service untuk mengelola Produk Siap Jual dengan stock gudang
 *
 * LOGIKA BARU (WAJIB):
 *
 * CREATE:
 * - Input HPP, Margin, Biaya lain-lain
 * - HITUNG harga jual otomatis
 * - JANGAN kurangi stock gudang (hanya saat user klik "Tambah Stock")
 * - Set stok_siap_jual = 0 (default)
 *
 * EXTENSION: Support untuk tipe_produk (single/paket)
 * - Single: menggunakan stock_gudang_id (existing)
 * - Paket: menggunakan produk_paket_id (NEW)
 */
class ProdukSiapJualService
{
    protected StockMovementService $movementService;

    public function __construct(StockMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    /**
     * Create produk siap jual
     *
     * EXTENSION: Support untuk tipe_produk (single/paket)
     * - Single: HPP dari stockGudang
     * - Paket: HPP dari produkPaket.hpp_total
     *
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $tipeProduk = $data['tipe_produk'] ?? 'single';

            // === EXTENSION: Handle paket type ===
            if ($tipeProduk === 'paket') {
                return $this->createFromPaket($data);
            }

            // === EXISTING LOGIC: Handle single type ===
            return $this->createFromSingle($data);
        });
    }

    /**
     * Create produk siap jual dari Stock Gudang (tipe single)
     * EXISTING LOGIC - TIDAK DIUBAH
     *
     * @param array $data
     * @return Model
     */
    protected function createFromSingle(array $data): Model
    {
        $stockGudang = StockGudang::findOrFail($data['stock_gudang_id']);

        // Hitung harga beli per PCS dari stock gudang (HANYA BAHAN BAKU)
        $hargaBeliPack = $stockGudang->harga_beli_pack ?? 0;
        $konversiSatuan = $stockGudang->konversi_satuan ?? 1;
        $hargaBeliPcs = $konversiSatuan > 0 ? $hargaBeliPack / $konversiSatuan : 0;

        // Ambil pcs_per_paket (isi PCS per paket)
        $pcsPerPaket = (int) ($data['pcs_per_paket'] ?? $data['isi_pcs_per_paket'] ?? 1);

        // 1️⃣ HPP PER PCS = HANYA HARGA BAHAN (TIDAK ADA BIAYA LAIN)
        $hppPerPcs = (float) ($data['hpp_per_pcs'] ?? $hargaBeliPcs);

        // 2️⃣ BIAYA LAIN-LAIN = BIAYA PER PAKET (BATCH)
        $biayaPacking = (float) ($data['biaya_packing'] ?? 0);
        $biayaSaos = (float) ($data['biaya_saos'] ?? 0);
        $biayaSumpit = (float) ($data['biaya_sumpit'] ?? 0);
        $biayaTenaga = (float) ($data['biaya_tenaga'] ?? 0);
        $totalBiayaLain = $biayaPacking + $biayaSaos + $biayaSumpit + $biayaTenaga;

        // 3️⃣ MARGIN LABA
        $marginLaba = (float) ($data['margin_laba'] ?? 0);

        // 4️⃣ PERHITUNGAN HPP PAKET
        // HPP Paket = HPP per PCS × jumlah PCS dalam paket
        $hppPaket = $hppPerPcs * $pcsPerPaket;

        // 5️⃣ MODAL PAKET = HPP PAKET + TOTAL BIAYA LAIN
        $modalPaket = $hppPaket + $totalBiayaLain;

        // 6️⃣ HARGA JUAL PAKET = MODAL PAKET × (1 + MARGIN / 100)
        $hargaJualPerPaket = $modalPaket * (1 + ($marginLaba / 100));

        // 7️⃣ HARGA JUAL PER PCS = HARGA JUAL PAKET / PCS PER PAKET
        $hargaJualPerPcs = $hargaJualPerPaket / $pcsPerPaket;

        // Buat produk siap jual
        $produk = ProdukSiapJual::create([
            'user_id' => auth()->id(),
            'stock_gudang_id' => $data['stock_gudang_id'],
            'produk_paket_id' => null,           // Single: tidak ada paket
            'tipe_produk' => 'single',           // Flag tipe produk
            'nama_produk' => $data['nama_produk'] ?? $stockGudang->nama_produk,
            'hpp_per_pcs' => $hppPerPcs, // HANYA HARGA BAHAN, TIDAK ADA BIAYA LAIN
            'margin_laba' => $marginLaba,
            'biaya_packing' => $biayaPacking,
            'biaya_saos' => $biayaSaos,
            'biaya_sumpit' => $biayaSumpit,
            'biaya_tenaga' => $biayaTenaga,
            'total_biaya_lain' => $totalBiayaLain,
            'hpp_total_per_pcs' => $hppPerPcs, // TETAP SAMA DENGAN HPP_PER_PCS (tidak ada biaya lain)
            'harga_jual_per_pcs' => $hargaJualPerPcs,
            'harga_jual_per_paket' => $hargaJualPerPaket,
            'harga_jual' => $hargaJualPerPaket, // Sama dengan harga_jual_per_paket
            'pcs_per_paket' => $pcsPerPaket,
            'stok_siap_jual' => 0, // DEFAULT: 0 (belum ada paket yang ready)
            'stok_pcs' => 0,
            'gambar_produk' => $this->storeGambarProduk($data['gambar_produk'] ?? null),
        ]);

        // ❌ JANGAN kurangi stock gudang saat create
        // Stock gudang HANYA berkurang saat user klik "Tambah Stock"

        return $produk;
    }

    /**
     * Create produk siap jual dari Produk Paket (tipe paket)
     * NEW EXTENSION - Backward compatible
     *
     * @param array $data
     * @return Model
     */
    protected function createFromPaket(array $data): Model
    {
        $produkPaket = ProdukPaket::with('details.stockGudang')
            ->findOrFail($data['produk_paket_id']);

        // Hitung HPP dari paket (SUM dari semua komponen)
        $hppTotal = $produkPaket->hitungHppTotal();

        // Ambil pcs_per_paket (untuk paket, ini adalah "berapa porsi dalam 1 paket jual")
        $pcsPerPaket = (int) ($data['pcs_per_paket'] ?? 1);

        // 1️⃣ HPP PER PCS = HPP PAKET TOTAL / pcs_per_paket
        // Untuk paket, hpp_per_pcs adalah hpp total paket dibagi pcs_per_paket
        $hppPerPcs = (float) ($data['hpp_per_pcs'] ?? ($hppTotal / max(1, $pcsPerPaket)));

        // 2️⃣ BIAYA LAIN-LAIN = BIAYA PER PAKET (BATCH)
        $biayaPacking = (float) ($data['biaya_packing'] ?? 0);
        $biayaSaos = (float) ($data['biaya_saos'] ?? 0);
        $biayaSumpit = (float) ($data['biaya_sumpit'] ?? 0);
        $biayaTenaga = (float) ($data['biaya_tenaga'] ?? 0);
        $totalBiayaLain = $biayaPacking + $biayaSaos + $biayaSumpit + $biayaTenaga;

        // 3️⃣ MARGIN LABA
        $marginLaba = (float) ($data['margin_laba'] ?? 0);

        // 4️⃣ PERHITUNGAN HPP PAKET
        // Untuk paket, HPP Paket = hpp_per_pcs × pcs_per_paket
        $hppPaket = $hppPerPcs * $pcsPerPaket;

        // 5️⃣ MODAL PAKET = HPP PAKET + TOTAL BIAYA LAIN
        $modalPaket = $hppPaket + $totalBiayaLain;

        // 6️⃣ HARGA JUAL PAKET = MODAL PAKET × (1 + MARGIN / 100)
        $hargaJualPerPaket = $modalPaket * (1 + ($marginLaba / 100));

        // 7️⃣ HARGA JUAL PER PCS = HARGA JUAL PAKET / PCS PER PAKET
        $hargaJualPerPcs = $hargaJualPerPaket / max(1, $pcsPerPaket);

        // Buat produk siap jual dengan tipe paket
        $produk = ProdukSiapJual::create([
            'user_id' => auth()->id(),
            'stock_gudang_id' => null,           // Paket: tidak ada stock gudang langsung
            'produk_paket_id' => $data['produk_paket_id'],  // FK ke produk paket
            'tipe_produk' => 'paket',            // Flag tipe produk
            'nama_produk' => $data['nama_produk'] ?? $produkPaket->nama_paket,
            'hpp_per_pcs' => $hppPerPcs,         // HPP per PCS (dari total paket)
            'margin_laba' => $marginLaba,
            'biaya_packing' => $biayaPacking,
            'biaya_saos' => $biayaSaos,
            'biaya_sumpit' => $biayaSumpit,
            'biaya_tenaga' => $biayaTenaga,
            'total_biaya_lain' => $totalBiayaLain,
            'hpp_total_per_pcs' => $hppPerPcs,   // Sama dengan HPP_PER_PCS
            'harga_jual_per_pcs' => $hargaJualPerPcs,
            'harga_jual_per_paket' => $hargaJualPerPaket,
            'harga_jual' => $hargaJualPerPaket,  // Sama dengan harga_jual_per_paket
            'pcs_per_paket' => $pcsPerPaket,
            'stok_siap_jual' => 0,               // DEFAULT: 0 (belum ada paket yang ready)
            'stok_pcs' => 0,
            'gambar_produk' => $this->storeGambarProduk($data['gambar_produk'] ?? null),
        ]);

        // ❌ JANGAN kurangi stock gudang saat create
        // Stock komponen paket HANYA berkurang saat penjualan

        return $produk;
    }

    /**
     * Update produk siap jual
     *
     * LOGIKA BARU:
     * - Update HPP, Margin, Biaya
     * - HITUNG ulang harga jual otomatis
     * - JANGAN ubah stok
     *
     * @param ProdukSiapJual $produk
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function update(ProdukSiapJual $produk, array $data): Model
    {
        return DB::transaction(function () use ($produk, $data) {
            // Ambil pcs_per_paket (bisa berubah)
            $pcsPerPaket = (int) ($data['pcs_per_paket'] ?? $data['isi_pcs_per_paket'] ?? $produk->pcs_per_paket ?? 1);

            // 1️⃣ HPP PER PCS = HANYA HARGA BAHAN (TIDAK ADA BIAYA LAIN)
            $hppPerPcs = (float) ($data['hpp_per_pcs'] ?? $produk->hpp_per_pcs);

            // 2️⃣ BIAYA LAIN-LAIN = BIAYA PER PAKET (BATCH)
            $biayaPacking = (float) ($data['biaya_packing'] ?? $produk->biaya_packing ?? 0);
            $biayaSaos = (float) ($data['biaya_saos'] ?? $produk->biaya_saos ?? 0);
            $biayaSumpit = (float) ($data['biaya_sumpit'] ?? $produk->biaya_sumpit ?? 0);
            $biayaTenaga = (float) ($data['biaya_tenaga'] ?? $produk->biaya_tenaga ?? 0);
            $totalBiayaLain = $biayaPacking + $biayaSaos + $biayaSumpit + $biayaTenaga;

            // 3️⃣ MARGIN LABA
            $marginLaba = (float) ($data['margin_laba'] ?? $produk->margin_laba ?? 0);

            // 4️⃣ PERHITUNGAN HPP PAKET
            $hppPaket = $hppPerPcs * $pcsPerPaket;

            // 5️⃣ MODAL PAKET = HPP PAKET + TOTAL BIAYA LAIN
            $modalPaket = $hppPaket + $totalBiayaLain;

            // 6️⃣ HARGA JUAL PAKET = MODAL PAKET × (1 + MARGIN / 100)
            $hargaJualPerPaket = $modalPaket * (1 + ($marginLaba / 100));

            // 7️⃣ HARGA JUAL PER PCS = HARGA JUAL PAKET / PCS PER PAKET
            $hargaJualPerPcs = $hargaJualPerPaket / $pcsPerPaket;

            // Update produk (JANGAN ubah stok)
            $produk->update([
                'hpp_per_pcs' => $hppPerPcs, // HANYA HARGA BAHAN, TIDAK ADA BIAYA LAIN
                'margin_laba' => $marginLaba,
                'biaya_packing' => $biayaPacking,
                'biaya_saos' => $biayaSaos,
                'biaya_sumpit' => $biayaSumpit,
                'biaya_tenaga' => $biayaTenaga,
                'total_biaya_lain' => $totalBiayaLain,
                'hpp_total_per_pcs' => $hppPerPcs, // TETAP SAMA DENGAN HPP_PER_PCS
                'harga_jual_per_pcs' => $hargaJualPerPcs,
                'harga_jual_per_paket' => $hargaJualPerPaket,
                'harga_jual' => $hargaJualPerPaket, // Sama dengan harga_jual_per_paket
                'pcs_per_paket' => $pcsPerPaket,
                'gambar_produk' => $this->storeGambarProduk(
                    $data['gambar_produk'] ?? null,
                    $produk->gambar_produk
                ),
            ]);

            // ❌ JANGAN ubah stok_siap_jual
            // Stok HANYA berubah via "Tambah Stock" atau "Hapus Produk"

            return $produk->fresh();
        });
    }

    /**
     * Delete produk siap jual dan kembalikan stok ke gudang
     *
     * LOGIKA BARU:
     * - Jika ada stok_siap_jual, kembalikan ke stock gudang
     * - Catat di stock_movements (type: IN)
     *
     * @param ProdukSiapJual $produk
     * @return array
     * @throws \Exception
     */
    public function delete(ProdukSiapJual $produk): array
    {
        if ($produk->gambar_produk) {
            Storage::disk('public')->delete($produk->gambar_produk);
        }

        return $this->movementService->restoreStockOnDelete($produk);
    }

    /**
     * Simpan gambar produk ke public disk.
     *
     * @param mixed $gambar
     * @param string|null $oldPath
     * @return string|null
     */
    private function storeGambarProduk($gambar, ?string $oldPath = null): ?string
    {
        if (!$gambar instanceof UploadedFile) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $gambar->store('produk-siap-jual', 'public');
    }

    /**
     * Hitung total biaya lain-lain
     */
    private function calculateTotalBiaya(array $data): float
    {
        return (float) (
            ($data['biaya_packing'] ?? 0) +
            ($data['biaya_saos'] ?? 0) +
            ($data['biaya_sumpit'] ?? 0) +
            ($data['biaya_tenaga'] ?? 0)
        );
    }

    /**
     * Hitung HPP total per PCS (hpp_per_pcs + biaya_lain)
     */
    private function calculateHppTotal(array $data, float $hargaBeliPcs): float
    {
        $totalBiaya = $this->calculateTotalBiaya($data);
        return $hargaBeliPcs + $totalBiaya;
    }

    /**
     * Proses pemakaian peralatan/kemasan dengan validasi ketat & logging
     *
     * 🎯 LOGIKA:
     * 1. Validasi setiap peralatan adalah kategori "peralatan"
     * 2. Validasi stok cukup sebelum update
     * 3. UPDATE warehouse: pcs_terpakai += jumlah, pcs_sisa -= jumlah
     * 4. INSERT ke pemakaian_peralatan (riwayat)
     * 5. INSERT ke stock_movements (tracking)
     *
     * ⚠️ MENGGUNAKAN DATABASE TRANSACTION:
     * - Jika ada error di step manapun → ROLLBACK SEMUA
     * - Semua berhasil atau tidak sama sekali (atomic)
     *
     * @param ProdukSiapJual $produkSiapJual
     * @param array $peralatanData Format: [stock_gudang_id => jumlah_pakai, ...]
     * @return array Detail hasil dengan info sebelum/sesudah
     * @throws \Exception Jika validasi gagal
     */
    public function processEquipmentUsage(ProdukSiapJual $produkSiapJual, array $peralatanData): array
    {
        // DEBUG: Log start of process
        \Log::debug("🔍 processEquipmentUsage START", [
            'produk_id' => $produkSiapJual->id,
            'peralatan_data' => $peralatanData,
        ]);

        if (empty($peralatanData)) {
            return ['success' => false, 'message' => 'Tidak ada peralatan yang dipilih'];
        }

        try {
            return DB::transaction(function () use ($produkSiapJual, $peralatanData) {
            $results = [
                'success' => true,
                'message' => 'Pemakaian peralatan berhasil dicatat ke warehouse',
                'items_processed' => 0,
                'details' => [],
            ];

            foreach ($peralatanData as $stockGudangId => $jumlahPakai) {
                // ==================== STEP 1: VALIDASI INPUT ====================
                if ($stockGudangId <= 0 || $jumlahPakai <= 0) {
                    continue;
                }

                $jumlahPakaiInt = (int) $jumlahPakai;

                // ==================== STEP 2: AMBIL DATA DENGAN LOCK ====================
                try {
                    $stockGudang = StockGudang::with('category')
                        ->findOrFail($stockGudangId);
                } catch (\Exception $e) {
                    throw new \Exception("❌ Peralatan dengan ID {$stockGudangId} tidak ditemukan di database");
                }

                // ==================== STEP 3: VALIDASI KATEGORI ====================
                if (!$stockGudang->category || $stockGudang->category->jenis_kategori !== 'peralatan') {
                    throw new \Exception(
                        "❌ VALIDASI GAGAL: '{$stockGudang->nama_produk}' BUKAN peralatan/kemasan.\n" .
                        "Kategori saat ini: " . ($stockGudang->category?->nama_kategori ?? 'tidak ada') . "\n" .
                        "Hanya item dengan kategori tipe 'peralatan' yang bisa digunakan di sini."
                    );
                }

                // ==================== STEP 4: AMBIL STOK SAAT INI (REFRESH) ====================
                $pcsAwal = (int) ($stockGudang->pcs_awal ?? 0);           // ✅ TIDAK BOLEH BERUBAH
                $sisaStokSebelum = (int) ($stockGudang->pcs_sisa ?? 0);
                $pcsTermakaiSebelum = (int) ($stockGudang->pcs_terpakai ?? 0);

                // ==================== STEP 5: VALIDASI STOK CUKUP ====================
                if ($jumlahPakaiInt > $sisaStokSebelum) {
                    throw new \Exception(
                        "❌ STOK TIDAK MENCUKUPI!\n" .
                        "Peralatan: {$stockGudang->nama_produk}\n" .
                        "Diminta: {$jumlahPakaiInt} PCS\n" .
                        "Tersedia: {$sisaStokSebelum} PCS\n" .
                        "Kurang: " . ($jumlahPakaiInt - $sisaStokSebelum) . " PCS"
                    );
                }

                // ==================== STEP 6: HITUNG NILAI BARU ====================
                // ✅ LOGIKA PEMAKAIAN (BENAR):
                // - pcs_awal TIDAK BOLEH BERUBAH
                // - pcs_terpakai += jumlah yang dipakai
                // - pcs_sisa = pcs_awal - pcs_terpakai (AUTO CALCULATED)
                $pcsTermakaiBaru = $pcsTermakaiSebelum + $jumlahPakaiInt;
                $pcsSisaBaru = $pcsAwal - $pcsTermakaiBaru;  // ✅ Gunakan pcs_awal sebagai patokan

                // ==================== STEP 7: UPDATE STOCK GUDANG (CRITICAL) ====================
                try {
                    // DEBUG: Log sebelum update
                    \Log::debug("🔍 Sebelum update", [
                        'id' => $stockGudang->id,
                        'pcs_terpakai_old' => $stockGudang->pcs_terpakai,
                        'pcs_sisa_old' => $stockGudang->pcs_sisa,
                        'pcs_terpakai_new' => $pcsTermakaiBaru,
                        'pcs_sisa_new' => $pcsSisaBaru,
                    ]);

                    // Update menggunakan raw SQL untuk menghindari accessor/mutator issues
                    $rowsAffected = \DB::update(
                        'UPDATE stock_gudang SET pcs_terpakai = ?, pcs_sisa = ?, updated_at = ? WHERE id = ?',
                        [$pcsTermakaiBaru, $pcsSisaBaru, now(), $stockGudang->id]
                    );

                    \Log::debug("🔍 Update result", [
                        'rows_affected' => $rowsAffected,
                    ]);

                    if (!$rowsAffected) {
                        throw new \Exception('Update gagal - no rows affected');
                    }

                    // Verify langsung dari database gunakan raw query
                    $verified = \DB::table('stock_gudang')
                        ->where('id', $stockGudang->id)
                        ->first();

                    // DEBUG: Log verification
                    \Log::debug("✅ Verified dari database", [
                        'pcs_terpakai_db' => $verified->pcs_terpakai,
                        'pcs_sisa_db' => $verified->pcs_sisa,
                        'match_terpakai' => (int)$verified->pcs_terpakai === $pcsTermakaiBaru,
                        'match_sisa' => (int)$verified->pcs_sisa === $pcsSisaBaru,
                    ]);

                    // Verify hasil update
                    if ((int)$verified->pcs_terpakai !== $pcsTermakaiBaru ||
                        (int)$verified->pcs_sisa !== $pcsSisaBaru) {
                        throw new \Exception(
                            'Database verification failed: ' .
                            "Expected: terpakai={$pcsTermakaiBaru}, sisa={$pcsSisaBaru}, " .
                            "Got: terpakai={$verified->pcs_terpakai}, sisa={$verified->pcs_sisa}"
                        );
                    }
                } catch (\Exception $e) {
                    throw new \Exception("❌ Gagal mengupdate stok peralatan '{$stockGudang->nama_produk}': " . $e->getMessage());
                }

                // ==================== STEP 8: CATAT RIWAYAT (PEMAKAIAN_PERALATAN) ====================
                try {
                    $pemakaian = \App\Models\PemakaianPeralatan::create([
                        'user_id' => auth()->id(),
                        'produk_siap_jual_id' => $produkSiapJual->id,
                        'stock_gudang_id' => $stockGudangId,
                        'jumlah_pakai' => $jumlahPakaiInt,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception("❌ Gagal mencatat riwayat di pemakaian_peralatan: " . $e->getMessage());
                }

                // ==================== STEP 9: CATAT PERGERAKAN STOK (STOCK_MOVEMENTS) ====================
                try {
                    $movement = $this->movementService->recordEquipmentUsage(
                        $stockGudang,
                        $produkSiapJual,
                        $jumlahPakaiInt,
                        "Pemakaian internal peralatan: {$jumlahPakaiInt} PCS dari {$stockGudang->nama_produk}"
                    );
                } catch (\Exception $e) {
                    \Log::warning("Stock movement gagal untuk pemakaian peralatan ID {$stockGudangId}: " . $e->getMessage());
                    // Jangan throw - ini hanya tracking, principal operation sudah berhasil
                }

                // ==================== STEP 10: SIMPAN HASIL ====================
                $results['items_processed']++;
                $results['details'][] = [
                    'nama_peralatan' => $stockGudang->nama_produk,
                    'jumlah_pakai' => $jumlahPakaiInt,
                    'pcs_terpakai_sebelum' => $pcsTermakaiSebelum,
                    'pcs_terpakai_sesudah' => $pcsTermakaiBaru,
                    'pcs_sisa_sebelum' => $sisaStokSebelum,
                    'pcs_sisa_sesudah' => $pcsSisaBaru,
                    'kategori' => $stockGudang->category?->nama_kategori ?? 'N/A',
                ];

                \Log::info(
                    "Equipment usage recorded",
                    [
                        'produk_siap_jual_id' => $produkSiapJual->id,
                        'stock_gudang_id' => $stockGudangId,
                        'jumlah_pakai' => $jumlahPakaiInt,
                        'pcs_terpakai_baru' => $pcsTermakaiBaru,
                        'pcs_sisa_baru' => $pcsSisaBaru,
                    ]
                );
            }

            // DEBUG: Log completion
            \Log::debug("✅ Transaction completed successfully", [
                'items_processed' => $results['items_processed'],
            ]);

            return $results;
            }); // Close the transaction closure
        } catch (\Exception $e) {
            // DEBUG: Log exception
            \Log::error("❌ Transaction exception", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }

    /**
     * Get equipment usage summary untuk reporting
     *
     * @param ProdukSiapJual|null $produkSiapJual null = all
     * @return array Summary dengan total & breakdown
     */
    public function getEquipmentUsageSummary(?ProdukSiapJual $produkSiapJual = null): array
    {
        $query = \App\Models\PemakaianPeralatan::with('stockGudang.category', 'user');

        if ($produkSiapJual) {
            $query->where('produk_siap_jual_id', $produkSiapJual->id);
        }

        $usages = $query->get();

        $summary = [
            'total_items' => 0,
            'total_pcs' => 0,
            'total_unique_equipment' => 0,
            'breakdown' => [],
            'by_category' => [],
        ];

        foreach ($usages as $usage) {
            $summary['total_items']++;
            $summary['total_pcs'] += $usage->jumlah_pakai;

            $equipmentName = $usage->stockGudang->nama_produk ?? 'Unknown';
            $categoryName = $usage->stockGudang->category?->nama_kategori ?? 'N/A';

            // Breakdown by equipment
            if (!isset($summary['breakdown'][$equipmentName])) {
                $summary['breakdown'][$equipmentName] = [
                    'total_pcs' => 0,
                    'count' => 0,
                    'category' => $categoryName,
                ];
                $summary['total_unique_equipment']++;
            }
            $summary['breakdown'][$equipmentName]['total_pcs'] += $usage->jumlah_pakai;
            $summary['breakdown'][$equipmentName]['count']++;

            // Breakdown by category
            if (!isset($summary['by_category'][$categoryName])) {
                $summary['by_category'][$categoryName] = 0;
            }
            $summary['by_category'][$categoryName] += $usage->jumlah_pakai;
        }

        return $summary;
    }

    /**
     * Verify equipment stock accuracy
     * Digunakan untuk debugging & quality assurance
     *
     * @param StockGudang $stockGudang
     * @return array Verification report
     */
    public function verifyEquipmentStock(StockGudang $stockGudang): array
    {
        $pcsAwal = (int) ($stockGudang->pcs_awal ?? $stockGudang->jumlah_pack ?? 0);
        if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
            $pcsAwal *= $stockGudang->konversi_satuan;
        }

        $pcsTerpakai = (int) ($stockGudang->pcs_terpakai ?? 0);
        $pcsSisa = (int) ($stockGudang->pcs_sisa ?? 0);

        // Expected: pcs_sisa = pcs_awal - pcs_terpakai
        $expectedSisa = $pcsAwal - $pcsTerpakai;

        // Sum dari pemakaian
        $totalPemakaian = \App\Models\PemakaianPeralatan::where('stock_gudang_id', $stockGudang->id)
            ->sum('jumlah_pakai');

        return [
            'stock_gudang_id' => $stockGudang->id,
            'nama_produk' => $stockGudang->nama_produk,
            'category' => $stockGudang->category?->nama_kategori ?? 'N/A',
            'pcs_awal' => $pcsAwal,
            'pcs_terpakai' => $pcsTerpakai,
            'pcs_sisa' => $pcsSisa,
            'pcs_sisa_expected' => $expectedSisa,
            'pcs_sisa_match' => $pcsSisa === $expectedSisa,
            'total_pemakaian_records' => $totalPemakaian,
            'errors' => [
                'sisa_mismatch' => $pcsSisa !== $expectedSisa ?
                    "pcs_sisa ({$pcsSisa}) != expected ({$expectedSisa})" : null,
                'total_exceeds_awal' => $pcsTerpakai > $pcsAwal ?
                    "pcs_terpakai ({$pcsTerpakai}) > pcs_awal ({$pcsAwal})" : null,
            ],
        ];
    }

    /**
     * Get all equipment usage history dengan filters
     *
     * @param array $filters ['product_id', 'equipment_id', 'date_from', 'date_to', 'user_id']
     * @param int $perPage Pagination
     * @return \Illuminate\Pagination\Paginator
     */
    public function getEquipmentUsageHistory(array $filters = [], int $perPage = 20)
    {
        $query = \App\Models\PemakaianPeralatan::with(
            'user',
            'produkSiapJual',
            'stockGudang.category'
        );

        if (!empty($filters['product_id'])) {
            $query->where('produk_siap_jual_id', $filters['product_id']);
        }

        if (!empty($filters['equipment_id'])) {
            $query->where('stock_gudang_id', $filters['equipment_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
