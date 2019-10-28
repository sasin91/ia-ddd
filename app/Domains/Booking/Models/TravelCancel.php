<?php

namespace App\Domains\Booking\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelCancel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'flight_number',
        'departs_at',
        'reason'
    ];

    protected $casts = [
        'departs_at' => 'datetime'
    ];

    public function departure()
    {
        return $this->belongsTo(Travel::class, 'flight_number');
    }

    /**
     * Whether this cancel affects the given date
     *
     * @param   string|DateTimeInterface  $date
     *
     * @return  bool
     */
    public function affects($date)
    {
        return $this->departs_at->format('Y-m-d H:i') === $this->asDateTime($date)->format('Y-m-d H:i');
    }
}
