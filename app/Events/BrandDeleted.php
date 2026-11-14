<?php

namespace App\Events;

use App\Models\Brand;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BrandDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Brand $brand)
    {
    }
}
