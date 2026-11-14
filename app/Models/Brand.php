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
}
