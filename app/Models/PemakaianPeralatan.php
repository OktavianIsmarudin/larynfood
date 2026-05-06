<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianPeralatan extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_peralatan';

    protected $fillable = [
        'user_id',
        'produk_siap_jual_id',
        'stock_gudang_id',
        'jumlah_pakai',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_pakai' => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function produkSiapJual(): BelongsTo
    {
        return $this->belongsTo(ProdukSiapJual::class, 'produk_siap_jual_id');
    }

    public function stockGudang(): BelongsTo
    {
        return $this->belongsTo(StockGudang::class, 'stock_gudang_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeByProdukSiapJual($query, $produkSiapJualId)
    {
        return $query->where('produk_siap_jual_id', $produkSiapJualId);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Dapatkan nama peralatan/kemasan
     */
    public function getNamaPeralatan(): string
    {
        return $this->stockGudang?->nama_produk ?? 'Tidak Diketahui';
    }

    /**
     * Dapatkan kategori peralatan
     */
    public function getKategori(): string
    {
        return $this->stockGudang?->category?->nama_kategori ?? '-';
    }

    /**
     * Dapatkan tanggal format Indonesia
     */
    public function getTanggalFormat(): string
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $month = (int)$this->created_at->format('m');
        $day = $this->created_at->format('d');
        $year = $this->created_at->format('Y');
        
        return "$day {$months[$month - 1]} $year";
    }
}
