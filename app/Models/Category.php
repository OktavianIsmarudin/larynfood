<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'deskripsi',
        'jenis_kategori',
    ];

    protected $casts = [
        'jenis_kategori' => 'string',
    ];

    /**
     * Relationship: Category belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Category has many StockGudang
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(StockGudang::class);
    }

    /**
     * Scope: Get categories for current user only
     */
    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    /**
     * Get jenis kategori label
     */
    public function getJenisKategoriLabel(): string
    {
        return match ($this->jenis_kategori) {
            'produk' => 'Bahan Baku',
            'peralatan' => 'Peralatan / Kemasan',
            default => $this->jenis_kategori,
        };
    }

    /**
     * Get jenis kategori badge color
     */
    public function getJenisKategoriBadgeColor(): string
    {
        return match ($this->jenis_kategori) {
            'produk' => 'success',
            'peralatan' => 'info',
            default => 'secondary',
        };
    }
}
