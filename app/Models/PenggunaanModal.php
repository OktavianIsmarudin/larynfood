<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggunaanModal extends Model
{
    use HasFactory;

    protected $table = 'penggunaan_modal';

    protected $fillable = [
        'user_id',
        'saldo_modal_id',
        'pembelian_id',
        'penjualan_id',
        'nominal',
        'jenis',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saldoModal(): BelongsTo
    {
        return $this->belongsTo(SaldoModal::class);
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopePengeluaran($query)
    {
        return $query->where('jenis', 'pengeluaran');
    }

    public function scopePemasukanKembali($query)
    {
        return $query->where('jenis', 'pemasukan_kembali');
    }
}
