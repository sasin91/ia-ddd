<?php

namespace App\Domains\Booking\Models;

use App\Domains\Aero\Models\AeroAction;
use App\Domains\Booking\Contracts\Changeable;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TravelPeriod;
use App\Domains\Booking\Models\Concerns\Serviceable;
use BenSampo\Enum\Traits\CastsEnums;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use function abs;
use function in_array;
use function with;

/**
 * Class Ticket
 * @package App\Domains\Booking\Models
 *
 * @property string $outward_flight_number
 * @property DateTimeInterface $outward_departure_datetime
 * @property DateTimeInterface outward_arrival_datetime
 * @property string $home_flight_number
 * @property DateTimeInterface|null $home_departure_datetime
 * @property DateTimeInterface|null $home_arrival_datetime
 * @property TravelPeriod|string $travel_period
 * @property TravelClass|string $travel_class
 * @property-read TicketPrice $price
 * @property-read Booking $booking
 * @property-read Passenger $passenger
 * @property-read Travel $outwardTravel
 * @property-read Travel|null $homeTravel
 */
class Ticket extends Model implements Changeable
{
    const CHANGE_FEE = 600;

    use CastsEnums, Serviceable, SoftDeletes;

    protected $fillable = [
        'outward_flight_number',
        'outward_departure_datetime',
        'outward_arrival_datetime',

        'home_flight_number',
        'home_departure_datetime',
        'home_arrival_datetime',

        'travel_period',
        'travel_class',

        'price_id',
        'booking_id',
        'passenger_id'
    ];

    protected $casts = [
        'outward_departure_datetime' => 'datetime',
        'outward_arrival_datetime' => 'datetime',

        'home_departure_datetime' => 'datetime',
        'home_arrival_datetime' => 'datetime',
    ];

    public $enumCasts = [
        'travel_period' => TravelPeriod::class,
        'travel_class' => TravelClass::class
    ];

    /**
     * The outward travel
     *
     * @return BelongsTo
     */
    public function outwardTravel(): BelongsTo
    {
        return $this->belongsTo(Travel::class, 'outward_flight_number', 'flight_number');
    }

    /**
     * The home travel
     *
     * @return BelongsTo
     */
    public function homeTravel(): BelongsTo
    {
        return $this->belongsTo(Travel::class, 'home_flight_number', 'flight_number');
    }

    /**
     * The booking this ticket is a part of.
     *
     * @return BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The passenger that's traveling with this ticket.
     *
     * @return BelongsTo
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    /**
     * The price for this ticket
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(TicketPrice::class);
    }

    /**
     * The commands that's been executed in the aero terminal
     *
     * @return HasMany
     */
    public function aeroActions(): HasMany
    {
        return $this->hasMany(AeroAction::class);
    }

    /**
     * The ticket changes that has been made to this ticket
     *
     * @return HasMany
     */
    public function ticketChanges(): HasMany
    {
        return $this->hasMany(TicketChange::class);
    }

    /**
     * Determine the fee for changing the model
     *
     * @param array $changes
     * @return int
     */
    public function determineChangeCost(array $changes): int
    {
        $fee = in_array($this->ticketChanges()->count(), [0,1]) ? static::CHANGE_FEE : 0;

        $cost = with($this->replicate()->applyChanges($changes), function (Ticket $changedTicket) {
            if ($this->period->isNot($changedTicket->period)) {
                return $changedTicket->price->amount;
            }

            return 0;
        });

        return abs($fee + $cost);
    }

    /**
     * Apply the changes to the model
     *
     * @param array $changes
     * @return $this
     */
    public function applyChanges(array $changes): Changeable
    {
        $this->fill($changes);

        if ($this->period->isNot(TravelPeriod::FLEX)) {
            $ticketPeriod = TravelPeriod::forTicket($this);

            if ($this->period->isNot($ticketPeriod)) {
                $this->period = $ticketPeriod;

                $this->price()->associate(
                    TicketPrice::forOutwardTicket($this)->first()
                );
            }
        }

        return $this;
    }
}
