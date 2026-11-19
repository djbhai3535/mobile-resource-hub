<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visitor_hash',
        'session_id',
        'ip_hash',
        'path',
        'route_name',
        'referer',
        'device_type',
        'browser',
        'platform',
        'visited_date',
        'created_at',
    ];

    protected $casts = [
        'visited_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
    ];

    /**
     * Scope query to a given date range.
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('visited_date', [$from, $to]);
    }
}
