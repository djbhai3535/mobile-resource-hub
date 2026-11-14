<?php

namespace App\Models;

use App\Events\DeviceDeleted;
use App\Events\DeviceSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'marketing_name',
        'slug',
        'product_description',
        'short_description',
        'featured_image',
        'image_gallery',
        'announced_date',
        'release_date',
        'status',
        'market_status',
        'os',
        'os_version',
        'device_category',
        'official_product_url',
        'is_featured',
        'sort_order',
        'seo_title',
        'meta_description',
        'canonical_url',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'announced_date' => 'date',
            'release_date' => 'date',
            'image_gallery' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($device) {
            if (empty($device->slug)) {
                $device->slug = Str::slug($device->name);
            }
        });

        static::saved(function ($device) {
            event(new DeviceSaved($device));
        });

        static::deleted(function ($device) {
            event(new DeviceDeleted($device));
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function modelNumbers(): HasMany
    {
        return $this->hasMany(DeviceModelNumber::class);
    }

    public function primaryModelNumber(): HasOne
    {
        return $this->hasOne(DeviceModelNumber::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(DeviceVariant::class);
    }

    public function specificationValues(): HasMany
    {
        return $this->hasMany(DeviceSpecificationValue::class);
    }

    public function firmwares(): HasMany
    {
        return $this->hasMany(Firmware::class);
    }

    public function flashFiles(): HasMany
    {
        return $this->hasMany(FlashFile::class);
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

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Retrieve non-empty specifications grouped by specification group.
     */
    public function getGroupedSpecifications(): array
    {
        $values = $this->specificationValues()
            ->with(['field.group'])
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->get();

        $grouped = [];

        foreach ($values as $val) {
            $field = $val->field;
            if (!$field || !$field->is_active || !$field->show_on_public) {
                continue;
            }

            $group = $field->group;
            if (!$group || !$group->is_active) {
                continue;
            }

            $groupId = $group->id;
            if (!isset($grouped[$groupId])) {
                $grouped[$groupId] = [
                    'group' => $group,
                    'fields' => [],
                ];
            }

            $grouped[$groupId]['fields'][] = [
                'field' => $field,
                'value' => $val->value,
                'additional_info' => $val->additional_info,
            ];
        }

        // Sort groups by group sort_order
        uasort($grouped, fn($a, $b) => $a['group']->sort_order <=> $b['group']->sort_order);

        // Sort fields within each group by field sort_order
        foreach ($grouped as &$g) {
            usort($g['fields'], fn($a, $b) => $a['field']->sort_order <=> $b['field']->sort_order);
        }

        return array_values($grouped);
    }
}
