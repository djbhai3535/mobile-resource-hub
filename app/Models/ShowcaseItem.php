<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShowcaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'brand_name',
        'video_path',
        'video_format',
        'poster_path',
        'sort_order',
        'is_active',
        'autoplay',
        'loop',
        'muted',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'autoplay' => 'boolean',
            'loop' => 'boolean',
            'muted' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope query to only include active showcase items.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to order items by sort_order ascending, then newest first.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    /**
     * Public absolute or asset URL for the video file.
     */
    public function getVideoUrlAttribute(): string
    {
        if (empty($this->video_path)) {
            return '';
        }

        if (Str::startsWith($this->video_path, ['http://', 'https://', '//'])) {
            return $this->video_path;
        }

        return asset('storage/' . ltrim($this->video_path, '/'));
    }

    /**
     * Public absolute or asset URL for the optional video poster.
     */
    public function getPosterUrlAttribute(): ?string
    {
        if (empty($this->poster_path)) {
            return null;
        }

        if (Str::startsWith($this->poster_path, ['http://', 'https://', '//'])) {
            return $this->poster_path;
        }

        return asset('storage/' . ltrim($this->poster_path, '/'));
    }
}
