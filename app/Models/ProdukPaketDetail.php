<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk Detail Item dalam Produk Paket
 * 
 * FUNGSI:
 * Menyimpan item-item yang menyusun sebuah paket beserta kuantitasnya.
 * Pivot table antara ProdukPaket dan StockGudang dengan tambahan qty.
 * 
 * CONTOH:
 * Paket "Nasi Box Spesial" memiliki detail:
 * - Nasi Putih: 200 pcs (gram)
 * - Ayam Goreng: 1 pcs
 * - Sambal: 1 pcs
 * - Kerupuk: 2 pcs
 * 
 * LOGIKA:
 * - qty_per_paket dalam satuan PCS (sudah dikonversi)
 * - Saat 1 paket dijual → kurangi stok masing-masing item sesuai qty
 */
class ProdukPaketDetail extends Model
{
    use HasFactory;

    protected $table = 'produk_paket_details';

    protected $fillable = [
        'produk_paket_id',
        'stock_gudang_id',
        'qty_per_paket',
        'keterangan',
    ];

    protected $casts = [
        'qty_per_paket' => 'decimal:2',
    ];

    /**
     * Relasi ke Produk Paket
     */
    public function produkPaket(): BelongsTo
    {
        return $this->belongsTo(ProdukPaket::class);
    }

    /**
     * Relasi ke Stock Gudang (item bahan)
     */
    public function stockGudang(): BelongsTo
    {
        return $this->belongsTo(StockGudang::class);
    }

    /**
     * Hitung HPP untuk item ini per paket
     * Formula: qty_per_paket × hpp_per_pcs dari stockGudang
     */
    public function getHppItemAttribute(): float
    {
        if (!$this->stockGudang) {
            return 0;
        }

        $hppPerPcs = $this->stockGudang->harga_beli_pcs ?? 
                     (($this->stockGudang->harga_beli_pack ?? 0) / max(1, $this->stockGudang->konversi_satuan));
        
        return round((float) $this->qty_per_paket * $hppPerPcs, 2);
    }

    /**
     * Accessor untuk nama item dari stock gudang
     */
    public function getNamaItemAttribute(): string
    {
        return $this->stockGudang->nama_produk ?? 'Item tidak ditemukan';
    }

    /**
     * Accessor untuk stok tersedia (pcs_sisa) dari stock gudang
     */
    public function getStokTersediaAttribute(): float
    {
        return (float) ($this->stockGudang->pcs_sisa ?? 0);
    }

    /**
     * Boot method untuk auto-update HPP paket saat detail berubah
     */
    protected static function boot()
    {
        parent::boot();

        // Setelah create/update/delete, update HPP total paket
        static::saved(function ($detail) {
            if ($detail->produkPaket) {
                $detail->produkPaket->updateHppTotal();
            }
        });

        static::deleted(function ($detail) {
            if ($detail->produkPaket) {
                $detail->produkPaket->updateHppTotal();
            }
        });
    }
}
