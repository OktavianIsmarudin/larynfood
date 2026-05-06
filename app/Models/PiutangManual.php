<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiutangManual extends Model
{
    use HasFactory;

    protected $table = 'piutang_manual';

    protected $fillable = [
        'user_id',
        'nama_pihak',
        'jenis',
        'nominal',
        'tanggal',
        'tanggal_jatuh_tempo',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopeHutang($query)
    {
        return $query->where('jenis', 'hutang');
    }

    public function scopePiutang($query)
    {
        return $query->where('jenis', 'piutang');
    }

    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'belum_lunas');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    // ========================================
    // HELPERS
    // ========================================

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isHutang(): bool
    {
        return $this->jenis === 'hutang';
    }

    public function isPiutang(): bool
    {
        return $this->jenis === 'piutang';
    }
}
