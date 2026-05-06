<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdukSiapJual extends Model
{
    use HasFactory;

    protected $table = 'produk_siap_juals';

    protected $fillable = [
        'user_id',
        'stock_gudang_id',
        'produk_paket_id', // NEW: untuk produk paket/platter
        'tipe_produk',     // NEW: 'single' atau 'paket'
        'nama_produk',
        'stok_pcs',
        'stok_siap_jual',
        'pcs_per_paket',
        'hpp_per_pcs',
        'margin_laba',
        'biaya_packing',
        'biaya_saos',
        'biaya_sumpit',
        'biaya_tenaga',
        'total_biaya_lain',
        'hpp_total_per_pcs',
        'harga_jual',
        'harga_jual_per_pcs',
        'harga_jual_per_paket',
        'jumlah_pcs_jual',
        'hpp_paket',
        'modal_paket',
        'jumlah_pcs',
        'total_hpp_modal',
        'gambar_produk',
        'is_published',
    ];

    protected $casts = [
        'hpp_per_pcs' => 'decimal:2',
        'margin_laba' => 'decimal:2',
        'biaya_packing' => 'decimal:2',
        'biaya_saos' => 'decimal:2',
        'biaya_sumpit' => 'decimal:2',
        'biaya_tenaga' => 'decimal:2',
        'total_biaya_lain' => 'decimal:2',
        'hpp_total_per_pcs' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'harga_jual_per_pcs' => 'decimal:2',
        'harga_jual_per_paket' => 'decimal:2',
        'jumlah_pcs_jual' => 'integer',
        'hpp_paket' => 'decimal:2',
        'modal_paket' => 'decimal:2',
        'jumlah_pcs' => 'integer',
        'total_hpp_modal' => 'decimal:2',
        'stok_pcs' => 'integer',
        'stok_siap_jual' => 'integer',
        'pcs_per_paket' => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockGudang(): BelongsTo
    {
        return $this->belongsTo(StockGudang::class, 'stock_gudang_id');
    }

    /**
     * Relasi ke Produk Paket (untuk tipe produk = 'paket')
     */
    public function produkPaket(): BelongsTo
    {
        return $this->belongsTo(ProdukPaket::class);
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'produk_siap_jual_id');
    }

    public function pemakaianPeralatan(): HasMany
    {
        return $this->hasMany(PemakaianPeralatan::class, 'produk_siap_jual_id')
            ->orderBy('created_at', 'desc');
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    // ==========================================
    // METHODS & ACCESSORS
    // ==========================================
    
    /**
     * Hitung harga beli per PCS dari stock gudang
     */
    public function getHargaBeliPcsAttribute(): float
    {
        if (!$this->stockGudang) {
            return $this->hpp_per_pcs ?? 0;
        }
        
        $hargaBeliPack = $this->stockGudang->harga_beli_pack ?? 0;
        $konversiSatuan = $this->stockGudang->konversi_satuan ?? 1;
        
        return $konversiSatuan > 0 ? (float) ($hargaBeliPack / $konversiSatuan) : 0;
    }

    /**
     * Hitung total HPP modal (harga_beli_pcs × jumlah_pcs)
     */
    public function getTotalHppModalAttribute(): float
    {
        return $this->total_hpp_modal ?? ($this->getHargaBeliPcsAttribute() * ($this->jumlah_pcs ?? 0));
    }

    /**
     * Dapatkan info stok sisa dari stock gudang terkait
     */
    public function getStockSisaAttribute(): int
    {
        return $this->stockGudang?->pcs_sisa ?? 0;
    }

    /**
     * Format tampilan modal
     */
    public function getDisplayModalAttribute(): string
    {
        $total = $this->total_hpp_modal ?? 0;
        return 'Rp ' . number_format($total, 0, ',', '.');
    }
    /**
     * Tambah stock siap jual dari stock gudang
     * 
     * Logika:
     * Tambah stock siap jual
     * 
     * FIX: Stock gudang TIDAK dikurangi di sini.
     * Stock gudang HANYA dikurangi saat TRANSAKSI PENJUALAN.
     * 
     * Logika:
     * 1. Input: jumlah_paket (contoh: 2 pack)
     * 2. Hitung total PCS: jumlah_paket × pcs_per_paket
     * 3. Validasi: pcs_sisa gudang >= total_pcs (informational)
     * 4. Tambah stok_siap_jual (dalam satuan paket)
     * 
     * @param int $jumlahPaket Jumlah PAKET yang ditambahkan (bukan PCS)
     * @return array Result dengan data updated
     * @throws Exception Jika validasi gagal
     */
    public function tambahStock(int $jumlahPaket): array
    {
        if ($jumlahPaket <= 0) {
            throw new \Exception('Jumlah paket harus lebih dari 0');
        }

        if (!$this->pcs_per_paket || $this->pcs_per_paket <= 0) {
            throw new \Exception('PCS per paket tidak dikonfigurasi dengan benar');
        }

        $stockGudang = $this->stockGudang;
        if (!$stockGudang) {
            throw new \Exception('Stock gudang tidak ditemukan');
        }

        // STEP 1: Hitung total PCS yang dibutuhkan
        $totalPcsNeeded = $jumlahPaket * $this->pcs_per_paket;

        // STEP 2: Ambil data stock gudang saat ini
        $pcsSisaGudang = $stockGudang->pcs_sisa ?? 0;

        // STEP 3: Validasi - stock gudang harus cukup (informational check)
        if ($pcsSisaGudang < $totalPcsNeeded) {
            throw new \Exception(
                "Stock gudang tidak cukup. Dibutuhkan: {$totalPcsNeeded} PCS, Tersedia: {$pcsSisaGudang} PCS"
            );
        }

        // ❌ TIDAK mengurangi stock gudang di sini
        // ✅ Stock gudang akan dikurangi HANYA saat TRANSAKSI PENJUALAN

        // STEP 4: Tambah stok_siap_jual (dalam satuan PAKET)
        $this->stok_siap_jual += $jumlahPaket;
        $this->save();

        return [
            'success' => true,
            'message' => "Berhasil tambah {$jumlahPaket} paket siap jual (Stok gudang akan dikurangi saat penjualan)",
            'stok_siap_jual' => $this->stok_siap_jual,
            'pcs_sisa_gudang' => $pcsSisaGudang, // Tidak berubah
        ];
    }

    // ==========================================
    // PRODUK PAKET METHODS
    // ==========================================
    
    /**
     * Cek apakah produk ini adalah tipe paket
     * Primary check: produk_paket_id IS NOT NULL
     */
    public function isPaket(): bool
    {
        return $this->produk_paket_id !== null;
    }

    /**
     * Cek apakah produk ini adalah tipe single (item tunggal)
     * Complement dari isPaket()
     */
    public function isSingle(): bool
    {
        return !$this->isPaket();
    }

    /**
     * Dapatkan HPP per pcs berdasarkan tipe produk
     * - Single: dari stockGudang
     * - Paket: dari produkPaket.hpp_total / pcs_per_paket
     */
    public function getHppPerPcsDinamis(): float
    {
        if ($this->isPaket() && $this->produkPaket) {
            $pcsPerPaket = max(1, $this->pcs_per_paket ?? 1);
            return round($this->produkPaket->hpp_total / $pcsPerPaket, 2);
        }
        
        return (float) ($this->hpp_per_pcs ?? 0);
    }

    /**
     * Validasi stok untuk penjualan
     * - Single: validasi stok_siap_jual saja (gudang sudah dikurangi saat Tambah Stock)
     * - Paket: validasi stok_siap_jual DAN stok gudang SETIAP komponen
     * 
     * @param int $jumlahPaket Jumlah paket yang akan dijual
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validasiStokPenjualan(int $jumlahPaket): array
    {
        $errors = [];

        // Validasi stok_siap_jual untuk kedua tipe
        if (($this->stok_siap_jual ?? 0) < $jumlahPaket) {
            $errors[] = "Stok siap jual tidak cukup. Tersedia: {$this->stok_siap_jual}, Diminta: {$jumlahPaket}";
        }

        // Untuk PAKET: validasi juga stok gudang setiap komponen
        if ($this->isPaket() && $this->produk_paket_id) {
            $produkPaket = ProdukPaket::with('details.stockGudang')->find($this->produk_paket_id);
            if ($produkPaket) {
                $cekStok = $produkPaket->cekStokCukup($jumlahPaket);
                if (!$cekStok['sufficient']) {
                    foreach ($cekStok['insufficient_items'] as $item) {
                        $errors[] = "Stok gudang {$item['nama']} tidak cukup. Dibutuhkan: {$item['dibutuhkan']} PCS, Tersedia: {$item['tersedia']} PCS";
                    }
                }
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Kurangi stok saat penjualan
     * - Single: HANYA kurangi stok_siap_jual (gudang sudah dikurangi saat Tambah Stock)
     * - Paket: kurangi stok_siap_jual DAN kurangi stok gudang SETIAP komponen
     * 
     * @param int $jumlahPaket Jumlah paket yang terjual
     * @throws \Exception jika stok tidak cukup
     */
    public function kurangiStokPenjualan(int $jumlahPaket): void
    {
        // Validasi dulu
        $validasi = $this->validasiStokPenjualan($jumlahPaket);
        if (!$validasi['valid']) {
            throw new \Exception(implode('. ', $validasi['errors']));
        }

        // Kurangi stok_siap_jual (berlaku untuk kedua tipe)
        $this->stok_siap_jual = max(0, ($this->stok_siap_jual ?? 0) - $jumlahPaket);
        $this->save();

        // === PAKET: kurangi stok gudang setiap komponen ===
        if ($this->isPaket() && $this->produk_paket_id) {
            $produkPaket = ProdukPaket::with('details')->findOrFail($this->produk_paket_id);
            $produkPaket->kurangiStok($jumlahPaket);
        }
        // Single: gudang TIDAK dikurangi di sini (sudah dikurangi saat Tambah Stock)
    }

    /**
     * Restore stok saat pembatalan penjualan
     * - Single: HANYA restore stok_siap_jual (gudang TIDAK di-restore)
     * - Paket: restore stok_siap_jual DAN restore stok gudang setiap komponen
     * 
     * @param int $jumlahPaket Jumlah paket yang dibatalkan
     */
    public function restoreStokPenjualan(int $jumlahPaket): void
    {
        // Restore stok_siap_jual (berlaku untuk kedua tipe)
        $this->stok_siap_jual = ($this->stok_siap_jual ?? 0) + $jumlahPaket;
        $this->save();

        // === PAKET: restore stok gudang setiap komponen ===
        if ($this->isPaket() && $this->produk_paket_id) {
            $produkPaket = ProdukPaket::with('details')->find($this->produk_paket_id);
            if ($produkPaket) {
                $produkPaket->restoreStok($jumlahPaket);
            }
        }
        // Single: gudang TIDAK di-restore (sudah dikurangi permanen saat Tambah Stock)
    }
}
