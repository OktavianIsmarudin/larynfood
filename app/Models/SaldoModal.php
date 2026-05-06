<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaldoModal extends Model
{
    use HasFactory;

    protected $table = 'saldo_modal';

    protected $fillable = [
        'user_id',
        'tanggal',
        'saldo_awal',
        'sumber_modal',
        'piutang_manual_id',
        'keterangan',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'tanggal' => 'date',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penggunaanModal(): HasMany
    {
        return $this->hasMany(PenggunaanModal::class);
    }

    public function piutangManual(): BelongsTo
    {
        return $this->belongsTo(PiutangManual::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Total penggunaan modal (pengeluaran)
     */
    public function getTotalPenggunaanAttribute(): float
    {
        return (float) $this->penggunaanModal()
            ->where('jenis', 'pengeluaran')
            ->sum('nominal');
    }

    /**
     * Total pemasukan kembali
     */
    public function getTotalPemasukanKembaliAttribute(): float
    {
        return (float) $this->penggunaanModal()
            ->where('jenis', 'pemasukan_kembali')
            ->sum('nominal');
    }

    /**
     * Saldo akhir = saldo_awal - total_penggunaan + total_pemasukan_kembali
     */
    public function getSaldoAkhirAttribute(): float
    {
        return (float) $this->saldo_awal - $this->total_penggunaan + $this->total_pemasukan_kembali;
    }
}
