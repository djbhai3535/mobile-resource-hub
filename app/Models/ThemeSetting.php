<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'is_published',
        'branding',
        'global_colors',
        'light_palette',
        'dark_palette',
        'typography',
        'layout',
        'glassmorphism',
        'buttons',
        'animations',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'branding' => 'array',
        'global_colors' => 'array',
        'light_palette' => 'array',
        'dark_palette' => 'array',
        'typography' => 'array',
        'layout' => 'array',
        'glassmorphism' => 'array',
        'buttons' => 'array',
        'animations' => 'array',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
