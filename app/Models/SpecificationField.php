<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SpecificationField extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'label',
        'key',
        'unit',
        'field_type',
        'help_text',
        'options',
        'is_required',
        'show_on_public',
        'is_filterable',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'show_on_public' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($field) {
            if (empty($field->key)) {
                $field->key = Str::slug($field->label, '_');
            }
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SpecificationGroup::class, 'group_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(DeviceSpecificationValue::class, 'field_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
