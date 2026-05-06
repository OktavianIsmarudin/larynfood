<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_bukti_pembelian',
        'supplier_id',
        'category_id',
        'nama_produk',
        'qty',
        'harga_satuan',
        'tipe_diskon',
        'diskon',
        'subtotal',
        'total_biaya_awal',
        'total_pengeluaran',
        'tanggal_pembelian',
        'bukti_pembelian',
        'status_stock',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'diskon' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_biaya_awal' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'tanggal_pembelian' => 'date',
        'status_stock' => 'string', // enum in database
    ];

    public function stockGudang()
    {
        return $this->hasOne(StockGudang::class, 'purchase_id');
    }

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

    public function scopeForUser($query, $userId = null)
    {
        // If userId is null, return all records (untuk super admin mode agregat)
        if ($userId === null) {
            return $query;
        }

        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pembelian', [$startDate, $endDate]);
    }

    /**
     * Generate nomor bukti pembelian dengan format INV-YYYY-NNN
     */
    public static function generateNomorBukti()
    {
        $tahun = date('Y');
        $lastPembelian = self::where('user_id', auth()->id())
            ->whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        $nomor = ($lastPembelian ? intval(substr($lastPembelian->nomor_bukti_pembelian, -3)) + 1 : 1);

        return 'INV-' . $tahun . '-' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }
}
