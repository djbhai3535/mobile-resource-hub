<?php

namespace App\Models;

use App\Events\FirmwareDeleted;
use App\Events\FirmwareSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Firmware extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'firmwares';

    protected $fillable = [
        'brand_id',
        'device_id',
        'title',
        'slug',
        'model_number',
        'compatible_models',
        'region',
        'csc',
        'android_version',
        'os_version',
        'firmware_version',
        'build_number',
        'security_patch_date',
        'release_date',
        'file_type',
        'archive_format',
        'file_size_bytes',
        'file_size_formatted',
        'md5_checksum',
        'sha256_checksum',
        'flashing_instructions',
        'required_tools',
        'prerequisites',
        'warnings',
        'known_issues',
        'source_website',
        'official_download_url',
        'license_type',
        'distribution_permission',
        'permission_notes',
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
            'compatible_models' => 'array',
            'security_patch_date' => 'date',
            'release_date' => 'date',
            'file_size_bytes' => 'integer',
            'is_verified' => 'boolean',
            'download_requests_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($firmware) {
            if (empty($firmware->slug)) {
                $firmware->slug = Str::slug($firmware->title . '-' . ($firmware->build_number ?: Str::random(5)));
            }
        });

        static::saved(function ($firmware) {
            event(new FirmwareSaved($firmware));
        });

        static::deleted(function ($firmware) {
            event(new FirmwareDeleted($firmware));
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
