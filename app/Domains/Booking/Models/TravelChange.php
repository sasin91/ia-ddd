<?php

namespace App\Domains\Booking\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelChange extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'flight_number',
        'departs_at',
        'modifications'
    ];

    protected $casts = [
        'departs_at' => 'datetime',
        'modifications' => 'array'
    ];

    /**
     * The departure/flight this change is related to
     *
     * @return  BelongsTo
     */
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

    /**
     * Apply the stored modifications to the given date(s)
     *
     * @param DateTimeInterface $date
     * @return  void
     */
    public function apply($date)
    {
        foreach (Arr::wrap($this->modifications) as $modification) {
            $date->modify($modification);
        }
    }
}
