<?php

namespace App\Events;

use App\Models\Driver;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Driver $driver)
    {
    }
}
