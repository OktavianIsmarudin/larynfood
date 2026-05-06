<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model untuk mencatat setiap pergerakan stok
 */
class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_movements';

    protected $fillable = [
        'user_id',
        'stock_gudang_id',
        'produk_siap_jual_id',
        'type', // OUT, IN, ADJUSTMENT
        'pcs',
        'keterangan',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'pcs' => 'integer',
        'reference_id' => 'integer',
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
        return $this->belongsTo(StockGudang::class);
    }

    public function produkSiapJual(): BelongsTo
    {
        return $this->belongsTo(ProdukSiapJual::class);
    }
}
