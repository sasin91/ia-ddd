<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TripService extends Pivot
{
    protected $table = 'ticket_service';

    protected $fillable = [
        'service_id',
        'ticket_id'
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
