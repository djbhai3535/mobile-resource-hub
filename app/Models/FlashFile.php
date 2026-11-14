<?php

namespace App\Models;

use App\Events\FlashFileDeleted;
use App\Events\FlashFileSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FlashFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'device_id',
        'title',
        'slug',
        'model_number',
        'chipset',
        'file_type',
        'android_version',
        'file_size_bytes',
        'file_size_formatted',
        'md5_checksum',
        'sha256_checksum',
        'flashing_tool',
        'compatibility_notes',
        'installation_instructions',
        'source_website',
        'official_download_url',
        'distribution_permission',
        'is_verified',
        'status',
        'download_requests_count',
        'seo_title',
        'meta_description',
        'canonical_url',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'is_verified' => 'boolean',
            'download_requests_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flashFile) {
            if (empty($flashFile->slug)) {
                $flashFile->slug = Str::slug($flashFile->title . '-' . ($flashFile->model_number ?: Str::random(5)));
            }
        });

        static::saved(function ($flashFile) {
            event(new FlashFileSaved($flashFile));
        });

        static::deleted(function ($flashFile) {
            event(new FlashFileDeleted($flashFile));
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function mirrors(): MorphMany
    {
        return $this->morphMany(DownloadMirror::class, 'mirrorable')->orderBy('sort_order');
    }

    public function getDownloadCountAttribute(): int
    {
        return (int) $this->download_requests_count;
    }

    public function downloadRequests(): MorphMany
    {
        return $this->morphMany(DownloadRequest::class, 'downloadable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', 'archived');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
