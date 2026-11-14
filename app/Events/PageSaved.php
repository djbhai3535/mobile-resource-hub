<?php

namespace App\Events;

use App\Models\Page;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Page $page)
    {
    }
}
