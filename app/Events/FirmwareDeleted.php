<?php

namespace App\Events;

use App\Models\Firmware;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FirmwareDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Firmware $firmware)
    {
    }
}
