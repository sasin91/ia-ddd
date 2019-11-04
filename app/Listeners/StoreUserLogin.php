<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Request;

class StoreUserLogin
{
    public function handle(Authenticated $event)
    {
        $event->user->logins()->createdToday()->firstOrCreate([
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }
}
