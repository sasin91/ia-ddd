<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelStopover extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'travel_id',
        'airport_id',
        'weekday',
        'arrival_time',
        'departure_time'
    ];

    public function departure()
    {
        return $this->belongsTo(Travel::class);
    }

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }
}

