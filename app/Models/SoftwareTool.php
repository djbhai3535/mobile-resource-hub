<?php

namespace App\Models;

use App\Events\SoftwareDeleted;
use App\Events\SoftwareSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SoftwareTool extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'software_tools';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'version',
        'developer',
        'supported_os',
        'supported_architectures',
        'description',
        'changelog',
        'release_date',
        'file_format',
        'file_size_bytes',
        'file_size_formatted',
        'md5_checksum',
        'sha256_checksum',
        'license_type',
        'distribution_permission',
        'official_website',
        'dependencies',
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

        static::creating(function ($software) {
            if (empty($software->slug)) {
                $software->slug = Str::slug($software->name . '-' . ($software->version ?: Str::random(4)));
            }
        });

        static::saved(function ($software) {
            event(new SoftwareSaved($software));
        });

        static::deleted(function ($software) {
            event(new SoftwareDeleted($software));
        });
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
