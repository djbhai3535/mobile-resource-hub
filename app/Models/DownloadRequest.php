<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DownloadRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'downloadable_type',
        'downloadable_id',
        'mirror_id',
        'ip_address',
        'user_agent',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function downloadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function mirror(): BelongsTo
    {
        return $this->belongsTo(DownloadMirror::class);
    }
}
