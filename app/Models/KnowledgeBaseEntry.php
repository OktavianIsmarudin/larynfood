<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeBaseEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'topik',
        'pertanyaan',
        'jawaban',
        'kata_kunci',
        'instruksi_ai',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForTopic(Builder $query, ?string $topic): Builder
    {
        if (! $topic) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($topic) {
            $inner->where('topik', $topic)
                ->orWhere('topik', 'umum');
        });
    }
}
