<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Needed for private/presence channel authentication used by Echo/Reverb.
        Broadcast::routes(['middleware' => ['web']]);
    }
}
