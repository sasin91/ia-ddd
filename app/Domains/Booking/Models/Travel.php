<?php

namespace App\Domains\Booking\Models;

use App\Collections\DateRangeCollection;
use App\Domains\Booking\TravelDate;
use App\Domains\Booking\Enums\TravelClass;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Travel / Flight
 *
 * @property-read DateRangeCollection $timestamps
 * @property-read Airport $departureAirport
 * @property-read Airport $destinationAirport
 * @property-read Collection<Seat> $seats
 * @property-read Collection<TravelTime> $times
 * @property-read Collection<TravelChange> $changes
 * @property-read Collection<TravelCancel> $cancels
 * @property-read Collection<Ticket> $tickets
 */
class Travel extends Model
{
    use CastsEnums, SoftDeletes;

    protected $fillable = [
        'flight_number',
        'travel_class',
        'departure_airport_id',
        'destination_airport_id',
        'default_seats',
        'open_until'
    ];

    protected $casts = [
        'open_until' => 'datetime'
    ];

    public $enumCasts = [
        'travel_class' => TravelClass::class
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($departure) {
            $departure->times()->delete();
        });
    }

    public function timestamps($fromDate, $toDate)
    {
        return TravelDate::range(
            $fromDate,
            $toDate,
            $this
        )->format($this->getDateFormat());
    }

    public function getAvailableTimestampsAttribute()
    {
        return $this->timestamps(
            $this->freshTimestamp(),
            $this->open_until
        );
    }

    /**
     * The departure Airport
     *
     * @return  BelongsTo
     */
    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    /**
     * The destination airport
     *
     * @return  BelongsTo
     */
    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    /**
     * All the seat records for this flight
     *
     * @return  HasMany
     */
    public function seats()
    {
        return $this->hasMany(Seat::class, 'flight_number');
    }

    /**
     * The time table for this flight
     *
     * @return  HasMany
     */
    public function times()
    {
        return $this->hasMany(TravelTime::class, 'travel_id')->latest();
    }

    /**
     * The temporary landings for this flight
     *
     * @return  HasMany
     */
    public function stopovers()
    {
        return $this->hasMany(TravelStopover::class, 'travel_id');
    }

    /**
     * The changes for departure times related to this flight
     *
     * @return  HasMany
     */
    public function changes()
    {
        return $this->hasMany(TravelChange::class, 'flight_number', 'flight_number');
    }

    /**
     * The cancels for departure times related to this flight
     *
     * @return  HasMany
     */
    public function cancels()
    {
        return $this->hasMany(TravelCancel::class, 'flight_number', 'flight_number');
    }

    /**
     * The tickets that's flying out on this Travel
     *
     * @return  HasMany
     */
    public function outwardTickets()
    {
        return $this->hasMany(Ticket::class,'outward_flight_number', 'flight_number');
    }

    /**
     * The tickets that's flying home with this Travel
     *
     * @return  HasMany
     */
    public function homeTickets()
    {
        return $this->hasMany(Ticket::class,'home_flight_number', 'flight_number');
    }
}
