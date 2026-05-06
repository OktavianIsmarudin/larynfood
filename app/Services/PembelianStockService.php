<?php

namespace App\Services;

use App\Models\Pembelian;
use App\Models\StockGudang;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengelola integrasi Pembelian → Stock Gudang
 * 
 * Konsep:
 * - 1 Pembelian → 1 Stock Gudang (unique relationship)
 * - Pembelian status: belum_masuk_gudang → sudah_masuk_gudang
 * - Stock auto-calculate total_pcs, pcs_sisa
 */
class PembelianStockService
{
    /**
     * Simpan stock dari pembelian
     * 
     * LOGIKA PCS AWAL:
     * - Stock baru: pcs_awal = qty_pembelian, pcs_terpakai = 0, pcs_sisa = pcs_awal
     * - Stock ada: pcs_awal += qty_pembelian, pcs_sisa = pcs_awal - pcs_terpakai
     * 
     * Input dari form:
     * - purchase_id
     * - sku
     * - satuan
     * - konversi_satuan
     * - lokasi_gudang
     * 
     * Auto-fill dari pembelian:
     * - nama_produk
     * - supplier_id
     * - category_id
     * - jumlah_pack (dari pembelian.qty)
     * - harga_beli_pack (dari pembelian.total_pengeluaran / qty)
     */
    public function createStockFromPembelian(array $data): StockGudang
    {
        return DB::transaction(function () use ($data) {
            // Get pembelian
            $pembelian = Pembelian::findOrFail($data['purchase_id']);
            
            // Check jika sudah ada stock
            if ($pembelian->stockGudang) {
                throw new \Exception('Stock untuk pembelian ini sudah ada');
            }
            
            // Check status
            if ($pembelian->status_stock === 'sudah_masuk_gudang') {
                throw new \Exception('Pembelian sudah masuk gudang');
            }
            
            // Calculate harga per pack
            $hargaPerPack = $pembelian->qty > 0 
                ? $pembelian->total_pengeluaran / $pembelian->qty 
                : 0;
            
            // ✅ LOGIKA PCS AWAL (BENAR):
            // Saat pembelian pertama, pcs_awal = total qty yang dibeli
            $pcsAwal = $pembelian->qty * $data['konversi_satuan'];
            
            // Create stock
            $stock = StockGudang::create([
                'user_id' => auth()->id(),
                'purchase_id' => $pembelian->id,
                'supplier_id' => $pembelian->supplier_id,
                'category_id' => $pembelian->category_id,
                'nama_produk' => $pembelian->nama_produk,
                'satuan' => $data['satuan'],
                'konversi_satuan' => $data['konversi_satuan'],
                'jumlah_pack' => $pembelian->qty,
                'jumlah_stock' => $pembelian->qty, // backward compatibility
                'pcs_awal' => $pcsAwal,          // ✅ Total barang masuk dari pembelian
                'pcs_terpakai' => 0,              // ✅ Awal belum ada pemakaian
                'pcs_sisa' => $pcsAwal,           // ✅ Semua sisa (= pcs_awal - 0)
                'total_pcs' => $pcsAwal,          // backward compatibility
                'sisa_stock_pcs' => 0,            // backward compatibility
                'harga_beli_pack' => $hargaPerPack,
                'lokasi_gudang' => $data['lokasi_gudang'],
                'sku' => $data['sku'],
                'source' => 'pembelian',
                'status_stock' => 'sudah_masuk_gudang',
            ]);
            
            // Update pembelian status
            $pembelian->update([
                'status_stock' => 'sudah_masuk_gudang'
            ]);
            
            return $stock;
        });
    }

    /**
     * Update stock ketika pembelian di-update
     * 
     * LOGIKA PCS AWAL (ACCUMULATE):
     * - pcs_awal += qty_baru (bertambah setiap ada pembelian)
     * - pcs_sisa = pcs_awal - pcs_terpakai (recalculate otomatis)
     * - pcs_terpakai TIDAK BOLEH berubah
     * 
     * Restriction:
     * - Hanya bisa update jika status = 'sudah_masuk_gudang'
     * - Update: nama_produk, supplier_id, category_id, jumlah_pack, harga_beli_pack
     * - DILARANG: sku, satuan, konversi_satuan, pcs_terpakai, pcs_awal
     */
    public function updateStockFromPembelian(Pembelian $pembelian, array $data): StockGudang
    {
        return DB::transaction(function () use ($pembelian, $data) {
            // Check status
            if ($pembelian->status_stock === 'belum_masuk_gudang') {
                throw new \Exception('Tidak bisa update pembelian yang belum masuk gudang');
            }
            
            $stock = $pembelian->stockGudang;
            
            if (!$stock) {
                throw new \Exception('Stock untuk pembelian ini tidak ditemukan');
            }
            
            // Calculate harga per pack
            $hargaPerPack = $data['qty'] > 0 
                ? $data['total_pengeluaran'] / $data['qty'] 
                : 0;
            
            // ✅ LOGIKA PCS AWAL (ACCUMULATE):
            // Hitung pcs baru dari pembelian
            $pcsBaruDariPembelian = $data['qty'] * $stock->konversi_satuan;
            
            // Hitung pcs lama yang sudah tercatat dari pembelian sebelumnya
            $pcsLamaDariPembelian = $pembelian->qty * $stock->konversi_satuan;
            
            // Update pcs_awal dengan selisih (bisa naik atau turun)
            // Tapi pcs_terpakai tetap
            $pcsAwalBaru = ($stock->pcs_awal ?? 0) - $pcsLamaDariPembelian + $pcsBaruDariPembelian;
            
            // Recalculate pcs_sisa dengan pcs_terpakai yang tetap
            $pcsSisaBaru = $pcsAwalBaru - ($stock->pcs_terpakai ?? 0);
            
            // Update stock
            $stock->update([
                'nama_produk' => $data['nama_produk'],
                'supplier_id' => $data['supplier_id'],
                'category_id' => $data['category_id'],
                'jumlah_pack' => $data['qty'],
                'jumlah_stock' => $data['qty'], // backward compatibility
                'pcs_awal' => $pcsAwalBaru,      // ✅ ACCUMULATE (berubah dengan pembelian)
                'pcs_sisa' => $pcsSisaBaru,      // ✅ RECALCULATE (= pcs_awal - pcs_terpakai)
                'total_pcs' => $pcsAwalBaru,     // backward compatibility
                'harga_beli_pack' => $hargaPerPack,
            ]);
            
            return $stock;
        });
    }

    /**
     * Delete stock ketika pembelian dihapus
     * (cascade delete via database relationship)
     */
    public function deleteStockFromPembelian(Pembelian $pembelian): void
    {
        DB::transaction(function () use ($pembelian) {
            if ($pembelian->stockGudang) {
                $pembelian->stockGudang->delete();
            }
            
            // Reset status pembelian jika diperlukan
            $pembelian->update([
                'status_stock' => 'belum_masuk_gudang'
            ]);
        });
    }

    /**
     * Get stock data untuk autofill di form
     */
    public function getStockAutoFillFromPembelian(Pembelian $pembelian): array
    {
        return [
            'nama_produk' => $pembelian->nama_produk,
            'supplier_id' => $pembelian->supplier_id,
            'supplier_nama' => $pembelian->supplier->nama_supplier ?? '',
            'category_id' => $pembelian->category_id,
            'category_nama' => $pembelian->category->nama_kategori ?? '',
            'jumlah_pack' => $pembelian->qty,
            'harga_per_pack' => $pembelian->total_pengeluaran > 0 ? $pembelian->total_pengeluaran / $pembelian->qty : 0,
            'total_biaya' => $pembelian->total_pengeluaran,
        ];
    }
}
