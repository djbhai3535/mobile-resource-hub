<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'placement',
        'type',
        'image_path',
        'image_alt',
        'destination_url',
        'custom_html',
        'campaign_name',
        'start_date',
        'end_date',
        'priority',
        'rotation_weight',
        'show_desktop',
        'show_tablet',
        'show_mobile',
        'show_light_theme',
        'show_dark_theme',
        'is_active',
        'impressions_count',
        'clicks_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'priority' => 'integer',
        'rotation_weight' => 'integer',
        'show_desktop' => 'boolean',
        'show_tablet' => 'boolean',
        'show_mobile' => 'boolean',
        'show_light_theme' => 'boolean',
        'show_dark_theme' => 'boolean',
        'is_active' => 'boolean',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AdvertisementLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForPlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions_count <= 0) {
            return 0.0;
        }

        return round(($this->clicks_count / $this->impressions_count) * 100, 2);
    }
}
