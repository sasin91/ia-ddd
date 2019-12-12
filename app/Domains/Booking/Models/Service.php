<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'value',
        'cost',
        'icon',
        'aero_identifier',
        'details'
    ];

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class)->using(TripService::class);
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class)->using(TicketService::class);
    }
}
