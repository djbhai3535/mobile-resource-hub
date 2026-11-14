<?php

namespace App\Events;

use App\Models\FlashFile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlashFileDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FlashFile $flashFile)
    {
    }
}
