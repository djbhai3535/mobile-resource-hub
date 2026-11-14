<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'version_number',
        'settings_snapshot',
        'change_summary',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'settings_snapshot' => 'array',
        'published_at' => 'datetime',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
