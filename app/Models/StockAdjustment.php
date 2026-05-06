<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stock_adjustments';

    protected $fillable = [
        'stock_gudang_id',
        'user_id',
        'jumlah_pengurangan',
        'catatan',
    ];

    protected $casts = [
        'jumlah_pengurangan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: StockAdjustment belongs to StockGudang
     */
    public function stockGudang(): BelongsTo
    {
        return $this->belongsTo(StockGudang::class, 'stock_gudang_id');
    }

    /**
     * Relasi: StockAdjustment belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
