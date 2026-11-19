<?php

namespace App\Models;

use App\Events\BrandDeleted;
use App\Events\BrandSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'short_description',
        'full_description',
        'website_url',
        'country_of_origin',
        'status',
        'seo_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::saved(function ($brand) {
            event(new BrandSaved($brand));
        });

        static::deleted(function ($brand) {
            event(new BrandDeleted($brand));
        });
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function firmwares(): HasMany
    {
        return $this->hasMany(Firmware::class);
    }

    public function flashFiles(): HasMany
    {
        return $this->hasMany(FlashFile::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function publishedDevices(): HasMany
    {
        return $this->hasMany(Device::class)->where('status', 'published');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Resolves absolute or asset URL for brand logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        return asset('storage/' . ltrim($this->logo, '/'));
    }

    /**
     * Authentic brand visual colors and tokens for 27+ mobile brands.
     */
    public static function getBrandColorMeta(string $slug): array
    {
        $colors = [
            'samsung'      => ['color' => '#0c4da2', 'bg' => 'rgba(12, 77, 162, 0.08)', 'border' => 'rgba(12, 77, 162, 0.28)', 'badge' => '#0c4da2', 'text' => '#ffffff'],
            'apple'        => ['color' => '#1d1d1f', 'bg' => 'rgba(29, 29, 31, 0.06)', 'border' => 'rgba(29, 29, 31, 0.22)', 'badge' => '#1d1d1f', 'text' => '#ffffff'],
            'xiaomi'       => ['color' => '#ff6700', 'bg' => 'rgba(255, 103, 0, 0.09)', 'border' => 'rgba(255, 103, 0, 0.32)', 'badge' => '#ff6700', 'text' => '#ffffff'],
            'redmi'        => ['color' => '#e53935', 'bg' => 'rgba(229, 57, 53, 0.09)', 'border' => 'rgba(229, 57, 53, 0.32)', 'badge' => '#e53935', 'text' => '#ffffff'],
            'poco'         => ['color' => '#ffd200', 'bg' => 'rgba(255, 210, 0, 0.12)', 'border' => 'rgba(255, 210, 0, 0.40)', 'badge' => '#e6be00', 'text' => '#1a1a1a'],
            'oppo'         => ['color' => '#008a00', 'bg' => 'rgba(0, 138, 0, 0.08)', 'border' => 'rgba(0, 138, 0, 0.30)', 'badge' => '#008a00', 'text' => '#ffffff'],
            'vivo'         => ['color' => '#415fff', 'bg' => 'rgba(65, 95, 255, 0.08)', 'border' => 'rgba(65, 95, 255, 0.30)', 'badge' => '#415fff', 'text' => '#ffffff'],
            'realme'       => ['color' => '#ffb800', 'bg' => 'rgba(255, 184, 0, 0.12)', 'border' => 'rgba(255, 184, 0, 0.38)', 'badge' => '#f5a623', 'text' => '#1a1a1a'],
            'oneplus'      => ['color' => '#eb0028', 'bg' => 'rgba(235, 0, 40, 0.08)', 'border' => 'rgba(235, 0, 40, 0.30)', 'badge' => '#eb0028', 'text' => '#ffffff'],
            'google'       => ['color' => '#4285f4', 'bg' => 'rgba(66, 133, 244, 0.08)', 'border' => 'rgba(66, 133, 244, 0.30)', 'badge' => '#4285f4', 'text' => '#ffffff'],
            'google-pixel' => ['color' => '#4285f4', 'bg' => 'rgba(66, 133, 244, 0.08)', 'border' => 'rgba(66, 133, 244, 0.30)', 'badge' => '#4285f4', 'text' => '#ffffff'],
            'motorola'     => ['color' => '#0081c9', 'bg' => 'rgba(0, 129, 201, 0.08)', 'border' => 'rgba(0, 129, 201, 0.30)', 'badge' => '#0081c9', 'text' => '#ffffff'],
            'huawei'       => ['color' => '#cf0a2c', 'bg' => 'rgba(207, 10, 44, 0.08)', 'border' => 'rgba(207, 10, 44, 0.30)', 'badge' => '#cf0a2c', 'text' => '#ffffff'],
            'honor'        => ['color' => '#00a4e4', 'bg' => 'rgba(0, 164, 228, 0.08)', 'border' => 'rgba(0, 164, 228, 0.30)', 'badge' => '#00a4e4', 'text' => '#ffffff'],
            'nokia'        => ['color' => '#124191', 'bg' => 'rgba(18, 65, 145, 0.08)', 'border' => 'rgba(18, 65, 145, 0.30)', 'badge' => '#124191', 'text' => '#ffffff'],
            'infinix'      => ['color' => '#00c49f', 'bg' => 'rgba(0, 196, 159, 0.08)', 'border' => 'rgba(0, 196, 159, 0.30)', 'badge' => '#00a887', 'text' => '#ffffff'],
            'tecno'        => ['color' => '#0066ff', 'bg' => 'rgba(0, 102, 255, 0.08)', 'border' => 'rgba(0, 102, 255, 0.30)', 'badge' => '#0066ff', 'text' => '#ffffff'],
            'sony'         => ['color' => '#2b2b2b', 'bg' => 'rgba(43, 43, 43, 0.07)', 'border' => 'rgba(43, 43, 43, 0.25)', 'badge' => '#222222', 'text' => '#ffffff'],
            'asus'         => ['color' => '#00539b', 'bg' => 'rgba(0, 83, 155, 0.08)', 'border' => 'rgba(0, 83, 155, 0.30)', 'badge' => '#00539b', 'text' => '#ffffff'],
            'lenovo'       => ['color' => '#e2231a', 'bg' => 'rgba(226, 35, 26, 0.08)', 'border' => 'rgba(226, 35, 26, 0.30)', 'badge' => '#e2231a', 'text' => '#ffffff'],
            'zte'          => ['color' => '#0083ca', 'bg' => 'rgba(0, 131, 202, 0.08)', 'border' => 'rgba(0, 131, 202, 0.30)', 'badge' => '#0083ca', 'text' => '#ffffff'],
            'nothing'      => ['color' => '#d71920', 'bg' => 'rgba(215, 25, 32, 0.08)', 'border' => 'rgba(215, 25, 32, 0.32)', 'badge' => '#d71920', 'text' => '#ffffff'],
            'iqoo'         => ['color' => '#ff5722', 'bg' => 'rgba(255, 87, 34, 0.09)', 'border' => 'rgba(255, 87, 34, 0.32)', 'badge' => '#ff5722', 'text' => '#ffffff'],
            'meizu'        => ['color' => '#008cff', 'bg' => 'rgba(0, 140, 255, 0.08)', 'border' => 'rgba(0, 140, 255, 0.30)', 'badge' => '#008cff', 'text' => '#ffffff'],
            'itel'         => ['color' => '#e60012', 'bg' => 'rgba(230, 0, 18, 0.08)', 'border' => 'rgba(230, 0, 18, 0.30)', 'badge' => '#e60012', 'text' => '#ffffff'],
            'lava'         => ['color' => '#d32f2f', 'bg' => 'rgba(211, 47, 47, 0.08)', 'border' => 'rgba(211, 47, 47, 0.30)', 'badge' => '#d32f2f', 'text' => '#ffffff'],
            'htc'          => ['color' => '#85b817', 'bg' => 'rgba(133, 184, 23, 0.09)', 'border' => 'rgba(133, 184, 23, 0.32)', 'badge' => '#729f12', 'text' => '#ffffff'],
            'lg'           => ['color' => '#a50034', 'bg' => 'rgba(165, 0, 52, 0.08)', 'border' => 'rgba(165, 0, 52, 0.30)', 'badge' => '#a50034', 'text' => '#ffffff'],
        ];

        $cleanSlug = Str::slug($slug);
        return $colors[$cleanSlug] ?? [
            'color' => '#3b82f6',
            'bg' => 'rgba(59, 130, 246, 0.08)',
            'border' => 'rgba(59, 130, 246, 0.25)',
            'badge' => '#2563eb',
            'text' => '#ffffff',
        ];
    }

    public function getBrandMetaAttribute(): array
    {
        return self::getBrandColorMeta($this->slug);
    }
}
