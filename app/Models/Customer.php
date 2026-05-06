<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_customer',
        'kontak',
        'telepon',
        'email',
        'alamat',
        'kota',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Get total pembelian (sum of all penjualan for this customer)
     */
    public function getTotalPembelian()
    {
        return $this->penjualans()->sum('total_penjualan') ?? 0;
    }

    /**
     * Get count of transactions
     */
    public function getTransactionCount()
    {
        return $this->penjualans()->count() ?? 0;
    }

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopePiutang($query)
    {
        return $query->where('status_piutang', 'belum_lunas');
    }
}
