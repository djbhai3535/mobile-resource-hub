<?php

namespace App\Models;

use App\Events\DriverDeleted;
use App\Events\DriverSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'driver_version',
        'os',
        'windows_versions',
        'architecture',
        'release_date',
        'supported_devices',
        'file_type',
        'file_size_bytes',
        'file_size_formatted',
        'md5_checksum',
        'sha256_checksum',
        'official_source',
        'distribution_permission',
        'installation_instructions',
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
            'release_date' => 'date',
            'file_size_bytes' => 'integer',
            'is_verified' => 'boolean',
            'download_requests_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($driver) {
            if (empty($driver->slug)) {
                $driver->slug = Str::slug($driver->name . '-' . ($driver->driver_version ?: Str::random(4)));
            }
        });

        static::saved(function ($driver) {
            event(new DriverSaved($driver));
        });

        static::deleted(function ($driver) {
            event(new DriverDeleted($driver));
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
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
