<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'name',
        'ram',
        'internal_storage',
        'color',
        'market_region',
        'model_reference',
        'notes',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
