<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Lab404\Impersonate\Impersonate;
use Spatie\EventSourcing\Models\EloquentStoredEvent;

class StoredEvent extends EloquentStoredEvent
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($storedEvent) {
            /** @var Authenticatable|User $user */
            $user = Auth::user();

            if ($user) {
                $storedEvent->meta_data['user_id'] = $user->id;

                if ($impersonatorId = Impersonate::getImpersonatorId()) {
                    $storedEvent->meta_data['impersonator_id'] = $impersonatorId;
                }
            }
        });
    }
}
