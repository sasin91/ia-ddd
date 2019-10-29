<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

class StoreUserLogin
{
    public function handle(Authenticated $event)
    {
        $event->user->logins()->create([
           'ip_address' => Request::ip(),
           'user_agent' => Request::userAgent()
        ]);
    }
}
