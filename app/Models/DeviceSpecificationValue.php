<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSpecificationValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'field_id',
        'value',
        'additional_info',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(SpecificationField::class, 'field_id');
    }
}
