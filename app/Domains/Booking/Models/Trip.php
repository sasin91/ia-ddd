<?php

namespace App\Domains\Booking\Models;

use App\Domains\Aero\Enums\AeroActionType;
use App\Domains\Aero\Models\AeroAction;
use App\Domains\Booking\Contracts\Changeable;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TripType;
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
 * Class Trip
 * @package App\Domains\Booking\Models
 *
 * @property string $outward_travel
 * @property DateTimeInterface $outward_departure_datetime
 * @property DateTimeInterface outward_arrival_datetime
 * @property string $home_travel
 * @property DateTimeInterface|null $home_departure_datetime
 * @property DateTimeInterface|null $home_arrival_datetime
 * @property TripType|string $type
 * @property TravelClass|string $travel_class
 * @property-read Price $price
 * @property-read Ticket $ticket
 * @property-read Passenger $passenger
 * @property-read Travel $outwardTravel
 * @property-read Travel|null $homeTravel
 */
class Trip extends Model implements Changeable
{
    const CHANGE_FEE = 600;

    use CastsEnums, Serviceable, SoftDeletes;

    protected $fillable = [
        'outward_travel',
        'outward_departure_datetime',
        'outward_arrival_datetime',

        'home_travel',
        'home_departure_datetime',
        'home_arrival_datetime',

        'type',
        'travel_class',

        'price_id',
        'PNR',
        'passenger_id'
    ];

    protected $casts = [
        'outward_departure_datetime' => 'datetime',
        'outward_arrival_datetime' => 'datetime',

        'home_departure_datetime' => 'datetime',
        'home_arrival_datetime' => 'datetime',
    ];

    public $enumCasts = [
        'type' => TripType::class,
        'travel_class' => TravelClass::class
    ];

    /**
     * Whether the ticket has been recorded in atleast one terminal
     *
     * @return boolean
     */
    public function getIsRecordedAttribute(): bool
    {
        return $this
            ->aeroActions()
            ->executed()
            ->where('type', AeroActionType::ADDED)
            ->exists();
    }

    /**
     * The outward travel
     *
     * @return BelongsTo
     */
    public function outwardTravel(): BelongsTo
    {
        return $this->belongsTo(Travel::class, 'outward_travel', 'flight_number');
    }

    /**
     * The home travel
     *
     * @return BelongsTo
     */
    public function homeTravel(): BelongsTo
    {
        return $this->belongsTo(Travel::class, 'home_travel', 'flight_number');
    }

    /**
     * The ticket this trip is a part of.
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
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
     * The price for this trip
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
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

        $cost = with($this->replicate()->applyChanges($changes), function (Trip $trip) {
            if ($this->type->isNot($trip->type)) {
                return $trip->price->amount;
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

        if ($this->type->isNot(TripType::FLEX)) {
            $type = TripType::forTrip($this);

            if ($this->type->isNot($type)) {
                $this->type = $type;

                $this->price()->associate(
                    Price::forTrip($this)->first()
                );
            }
        }

        return $this;
    }
}
