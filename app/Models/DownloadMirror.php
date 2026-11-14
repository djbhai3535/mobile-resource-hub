<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DownloadMirror extends Model
{
    use HasFactory;

    protected $fillable = [
        'mirrorable_type',
        'mirrorable_id',
        'mirror_name',
        'url',
        'file_path',
        'disk',
        'is_official',
        'is_active',
        'sort_order',
        'download_requests_count',
    ];

    protected function casts(): array
    {
        return [
            'is_official' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'download_requests_count' => 'integer',
        ];
    }

    public function mirrorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function downloadRequests(): HasMany
    {
        return $this->hasMany(DownloadRequest::class, 'mirror_id');
    }

    public function isLocal(): bool
    {
        return !empty($this->file_path);
    }

    public function isExternal(): bool
    {
        return !empty($this->url);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
