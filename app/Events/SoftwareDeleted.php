<?php

namespace App\Events;

use App\Models\SoftwareTool;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SoftwareDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SoftwareTool $software)
    {
    }
}
