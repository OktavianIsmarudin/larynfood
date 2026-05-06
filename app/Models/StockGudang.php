<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockGudang extends Model
{
    use HasFactory;

    protected $table = 'stock_gudang';

    protected $fillable = [
        'user_id',
        'purchase_id',
        'supplier_id',
        'category_id',
        'nama_produk',
        'satuan',
        'konversi_satuan',
        'jumlah_pack',
        'jumlah_stock', // deprecated, keeping for backward compatibility
        'sisa_stock_pcs',
        'pcs_awal',      // Total akumulasi barang masuk (TIDAK BERKURANG)
        'pcs_terpakai',  // Barang yang sudah dipakai/dijual
        'pcs_sisa',      // Tersisa: pcs_awal - pcs_terpakai (AUTO CALCULATED)
        'total_pcs',     // deprecated, ganti dengan pcs_awal
        'lokasi_gudang',
        'sku',
        'gambar_produk',
        'harga_beli_pack',
        'source',
        'status_stock',
        'energi_kkal',
        'protein_g',
        'lemak_g',
        'karbohidrat_g',
    ];

    protected $casts = [
        'konversi_satuan' => 'integer',
        'jumlah_pack' => 'integer',
        'jumlah_stock' => 'integer',
        'sisa_stock_pcs' => 'integer',
        'pcs_awal' => 'integer',      // Total akumulasi barang
        'pcs_terpakai' => 'integer',  // Barang yang dipakai/dijual
        'pcs_sisa' => 'integer',      // Tersisa
        'total_pcs' => 'integer',     // deprecated
        'harga_beli_pack' => 'decimal:2',
        'energi_kkal' => 'decimal:2',
        'protein_g' => 'decimal:2',
        'lemak_g' => 'decimal:2',
        'karbohidrat_g' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class, 'purchase_id');
    }

    public function produkSiapJuals(): HasMany
    {
        return $this->hasMany(ProdukSiapJual::class, 'stock_gudang_id');
    }

    // Alias untuk kemudahan
    public function produkSiapJual()
    {
        return $this->produkSiapJuals();
    }

    /**
     * Relasi: StockGudang has many StockAdjustments (riwayat pengurangan stok manual)
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'stock_gudang_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relasi: StockGudang has many StockMovements
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'stock_gudang_id')
            ->orderBy('created_at', 'desc');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Dapatkan jenis kategori dari pembelian
     * Returns: 'produk' or 'peralatan'
     */
    public function getJenisKategori(): ?string
    {
        return $this->pembelian?->category?->jenis_kategori ?? $this->category?->jenis_kategori;
    }

    /**
     * Dapatkan label untuk jenis kategori
     * Returns display text untuk badge
     */
    public function getJenisKategoriLabel(): string
    {
        $jenisKategori = $this->getJenisKategori();
        return match ($jenisKategori) {
            'produk' => 'Produk / Bahan',
            'peralatan' => 'Peralatan / Kemasan',
            default => $jenisKategori ?? 'Tidak Ditentukan',
        };
    }

    /**
     * Dapatkan warna badge untuk jenis kategori
     * Returns Bootstrap color class
     */
    public function getJenisKategoriBadgeColor(): string
    {
        $jenisKategori = $this->getJenisKategori();
        return match ($jenisKategori) {
            'produk' => 'success',
            'peralatan' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Dapatkan icon untuk jenis kategori
     * Returns Font Awesome icon class
     */
    public function getJenisKategoriIcon(): string
    {
        $jenisKategori = $this->getJenisKategori();
        return match ($jenisKategori) {
            'produk' => 'fas fa-box',
            'peralatan' => 'fas fa-tools',
            default => 'fas fa-tag',
        };
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeFromPurchase($query)
    {
        return $query->whereNotNull('purchase_id');
    }

    public function scopeFromManual($query)
    {
        return $query->where('source', 'manual');
    }

    // ==========================================
    // METHODS
    // ==========================================
    
    /**
     * Hitung total PCS berdasarkan jumlah pack dan konversi satuan
     */
    public function calculateTotalPcs(): int
    {
        $pack = $this->jumlah_pack ?? $this->jumlah_stock ?? 0;
        return $pack * $this->konversi_satuan;
    }

    /**
     * Hitung PCS sisa berdasarkan total - terpakai
     */
    public function calculatePcsSisa(): int
    {
        $total = $this->total_pcs ?? $this->calculateTotalPcs();
        return $total - ($this->pcs_terpakai ?? 0);
    }

    /**
     * Hitung HPP per PCS
     */
    public function getHppPerPcs(): float
    {
        if (!$this->harga_beli_pack || $this->konversi_satuan <= 0) {
            return 0;
        }
        return (float) ($this->harga_beli_pack / $this->konversi_satuan);
    }

    /**
     * Format tampilan stok
     */
    public function getDisplayStock(): string
    {
        $pack = $this->jumlah_pack ?? $this->jumlah_stock ?? 0;
        $pcs = $this->pcs_sisa ?? 0;
        $konversi = $this->konversi_satuan ?? 1;
        
        return "{$pack} pack + {$pcs} pcs (1 pack = {$konversi} pcs)";
    }

    /**
     * Kurangi stok berdasarkan PCS yang dijual
     * Mengurangi pcs_sisa dan pcs_terpakai
     */
    public function reduceStockByPcs(int $pcsToSell): bool
    {
        $currentSisa = $this->pcs_sisa ?? $this->calculatePcsSisa();
        
        // Validasi stok cukup
        if ($pcsToSell > $currentSisa) {
            return false;
        }
        
        // Update pcs_sisa dan pcs_terpakai
        $newSisa = $currentSisa - $pcsToSell;
        $newTerpakai = ($this->pcs_terpakai ?? 0) + $pcsToSell;
        
        $this->update([
            'pcs_sisa' => $newSisa,
            'pcs_terpakai' => $newTerpakai,
        ]);
        
        return true;
    }

    /**
     * Restore stok ketika penjualan dibatalkan
     */
    public function restoreStockByPcs(int $pcsToRestore): bool
    {
        $newSisa = ($this->pcs_sisa ?? 0) + $pcsToRestore;
        $newTerpakai = max(0, ($this->pcs_terpakai ?? 0) - $pcsToRestore);
        
        $this->update([
            'pcs_sisa' => $newSisa,
            'pcs_terpakai' => $newTerpakai,
        ]);
        
        return true;
    }

    /**
     * Sinkronisasi pcs_terpakai berdasarkan pcs_awal - pcs_sisa
     * 
     * Memastikan pcs_terpakai selalu konsisten:
     * pcs_terpakai = pcs_awal - pcs_sisa
     * 
     * @param bool $save Simpan ke database jika ada perubahan
     * @return int Nilai pcs_terpakai yang benar
     */
    public function syncPcsTerpakai(bool $save = true): int
    {
        $pcsAwal = (int) ($this->pcs_awal ?? 0);
        $pcsSisa = (int) ($this->pcs_sisa ?? 0);
        $computedTerpakai = max(0, $pcsAwal - $pcsSisa);
        $storedTerpakai = (int) ($this->pcs_terpakai ?? 0);

        if ($storedTerpakai !== $computedTerpakai) {
            \Log::warning("⚠️ pcs_terpakai out of sync", [
                'stock_gudang_id' => $this->id,
                'nama_produk' => $this->nama_produk,
                'pcs_awal' => $pcsAwal,
                'pcs_sisa' => $pcsSisa,
                'stored_terpakai' => $storedTerpakai,
                'computed_terpakai' => $computedTerpakai,
            ]);

            $this->pcs_terpakai = $computedTerpakai;

            if ($save) {
                $this->saveQuietly();
            }
        }

        return $computedTerpakai;
    }

    /**
     * Accessor untuk backward compatibility
     */
    public function getTotalPcsAttribute(): int
    {
        return $this->total_pcs ?? $this->calculateTotalPcs();
    }

    public function getDisplayStockAttribute(): string
    {
        return $this->getDisplayStock();
    }
}


