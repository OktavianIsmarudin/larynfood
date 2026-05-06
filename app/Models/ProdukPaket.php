<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk Produk Paket/Platter
 * 
 * FUNGSI:
 * Menyimpan "resep/komposisi" paket yang terdiri dari beberapa item.
 * Contoh: "Nasi Box Spesial" = Nasi + Ayam + Sambal + Kerupuk
 * 
 * RELASI:
 * - BelongsTo User (pemilik)
 * - HasMany ProdukPaketDetail (komponen/item)
 * - HasMany ProdukSiapJual (produk yang menggunakan paket ini)
 * 
 * LOGIKA HPP:
 * - hpp_total = SUM(detail.qty_per_paket × stockGudang.hpp_per_pcs)
 * - Dihitung ulang setiap kali detail ditambah/diubah/dihapus
 */
class ProdukPaket extends Model
{
    use HasFactory;

    protected $table = 'produk_pakets';

    protected $fillable = [
        'user_id',
        'nama_paket',
        'kode_paket',
        'deskripsi',
        'hpp_total',
        'status',
    ];

    protected $casts = [
        'hpp_total' => 'decimal:2',
    ];

    /**
     * Relasi ke User (pemilik paket)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke detail item-item dalam paket
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProdukPaketDetail::class);
    }

    /**
     * Relasi ke produk siap jual yang menggunakan paket ini
     */
    public function produkSiapJuals(): HasMany
    {
        return $this->hasMany(ProdukSiapJual::class);
    }

    /**
     * Hitung ulang HPP total berdasarkan semua detail
     * 
     * Formula: SUM(qty_per_paket × hpp_per_pcs dari stockGudang)
     */
    public function hitungHppTotal(): float
    {
        $hppTotal = 0;

        foreach ($this->details()->with('stockGudang')->get() as $detail) {
            $stockGudang = $detail->stockGudang;
            if ($stockGudang) {
                // HPP per pcs dari stock gudang
                $hppPerPcs = $stockGudang->harga_beli_pcs ?? 
                             (($stockGudang->harga_beli_pack ?? 0) / max(1, $stockGudang->konversi_satuan));
                
                $hppTotal += (float) $detail->qty_per_paket * $hppPerPcs;
            }
        }

        return round($hppTotal, 2);
    }

    /**
     * Update HPP total dan simpan
     */
    public function updateHppTotal(): void
    {
        $this->hpp_total = (string) $this->hitungHppTotal();
        $this->save();
    }

    /**
     * Cek apakah semua item dalam paket memiliki stok cukup
     * untuk jumlah paket tertentu
     * 
     * @param int $jumlahPaket Jumlah paket yang ingin dijual
     * @return array ['sufficient' => bool, 'insufficient_items' => array]
     */
    public function cekStokCukup(int $jumlahPaket): array
    {
        $insufficientItems = [];
        $details = $this->details()->with('stockGudang')->get();

        foreach ($details as $detail) {
            $stockGudang = $detail->stockGudang;
            if (!$stockGudang) {
                $insufficientItems[] = [
                    'nama' => 'Item tidak ditemukan',
                    'stock_gudang_id' => $detail->stock_gudang_id,
                    'dibutuhkan' => $detail->qty_per_paket * $jumlahPaket,
                    'tersedia' => 0,
                    'kekurangan' => $detail->qty_per_paket * $jumlahPaket,
                ];
                continue;
            }

            $pcsNeeded = (float) $detail->qty_per_paket * $jumlahPaket;
            $pcsAvailable = (float) ($stockGudang->pcs_sisa ?? 0);

            if ($pcsNeeded > $pcsAvailable) {
                $insufficientItems[] = [
                    'nama' => $stockGudang->nama_produk,
                    'stock_gudang_id' => $detail->stock_gudang_id,
                    'dibutuhkan' => $pcsNeeded,
                    'tersedia' => $pcsAvailable,
                    'kekurangan' => $pcsNeeded - $pcsAvailable,
                ];
            }
        }

        return [
            'sufficient' => count($insufficientItems) === 0,
            'insufficient_items' => $insufficientItems,
        ];
    }

    /**
     * Kurangi stok semua item dalam paket
     * Menggunakan lockForUpdate() untuk konsistensi data
     * 
     * @param int $jumlahPaket Jumlah paket yang terjual
     * @throws \Exception jika stok tidak cukup
     */
    public function kurangiStok(int $jumlahPaket): void
    {
        // Ambil detail items dari paket
        $details = $this->details()->get();

        if ($details->isEmpty()) {
            throw new \Exception("Paket '{$this->nama_paket}' tidak memiliki komponen/detail item.");
        }

        // LOOP setiap komponen paket
        foreach ($details as $detail) {
            // Ambil stock gudang FRESH dengan lock (sama seperti produk single)
            $stockGudang = StockGudang::lockForUpdate()->findOrFail($detail->stock_gudang_id);

            // Hitung kebutuhan: qty_per_paket × jumlah_paket
            $kebutuhan = (float) $detail->qty_per_paket * $jumlahPaket;

            // Validasi: pcs_sisa harus >= kebutuhan
            $pcsSisa = (float) ($stockGudang->pcs_sisa ?? 0);
            if ($pcsSisa < $kebutuhan) {
                throw new \Exception(
                    "Stok gudang '{$stockGudang->nama_produk}' tidak cukup. " .
                    "Dibutuhkan: {$kebutuhan} PCS, Tersedia: {$pcsSisa} PCS"
                );
            }

            // Update: pcs_terpakai += kebutuhan, pcs_sisa -= kebutuhan
            $stockGudang->pcs_terpakai = ($stockGudang->pcs_terpakai ?? 0) + $kebutuhan;
            $stockGudang->pcs_sisa = max(0, $pcsSisa - $kebutuhan);

            // Update jumlah_pack (sama seperti produk single)
            if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
                $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
            }

            $stockGudang->save();
        }
    }

    /**
     * Restore stok semua item dalam paket (untuk pembatalan penjualan)
     * Menggunakan lockForUpdate() untuk konsistensi data
     * 
     * @param int $jumlahPaket Jumlah paket yang dibatalkan
     */
    public function restoreStok(int $jumlahPaket): void
    {
        $details = $this->details()->get();

        foreach ($details as $detail) {
            // Ambil stock gudang FRESH dengan lock (sama seperti produk single)
            $stockGudang = StockGudang::lockForUpdate()->findOrFail($detail->stock_gudang_id);

            $pcsToRestore = (float) $detail->qty_per_paket * $jumlahPaket;

            // Restore: pcs_terpakai -= restore, pcs_sisa += restore
            $stockGudang->pcs_terpakai = max(0, ($stockGudang->pcs_terpakai ?? 0) - $pcsToRestore);
            $stockGudang->pcs_sisa = ($stockGudang->pcs_sisa ?? 0) + $pcsToRestore;

            // Update jumlah_pack (sama seperti produk single)
            if ($stockGudang->konversi_satuan && $stockGudang->konversi_satuan > 0) {
                $stockGudang->jumlah_pack = (int) floor($stockGudang->pcs_sisa / $stockGudang->konversi_satuan);
            }

            $stockGudang->save();
        }
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
