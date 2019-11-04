<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'travel_id',
        'weekday',
        'departure_time',
        'arrival_time'
    ];

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }
}

