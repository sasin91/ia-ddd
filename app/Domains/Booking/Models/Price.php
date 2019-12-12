<?php

namespace App\Domains\Booking\Models;

use App\Domains\Booking\Enums\TripType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Price
 * @package App\Domains\Booking\Models
 *
 * @method static Builder forTrip(Trip $trip)
 */
class Price extends Model
{
    use CastsEnums, SoftDeletes;

    protected $fillable = [
        'travel_id',
        'price_season_id',
        'type',
        'age_group',
        'currency',
        'amount'
    ];

    public $enumCasts = [
        'type' => TripType::class
    ];


    /**
     * Get the travel prices that matches given ticket
     *
     * @param Builder|Price $query
     * @param Trip $trip
     * @return void
     */
    public function scopeForTrip($query, Trip $trip): void
    {
        $query
            ->where('travel_id', $trip->outwardTravel->id)
            ->where('type', $trip->period)
            ->where('age_group', $trip->passenger->age_group)
            ->whereHas('season', function ($season) use ($trip) {
                /** @var Builder|PriceSeason $season */
                $season->coversTicket($trip);
            });
    }

    /**
     * The travel this price is associated with
     *
     * @return BelongsTo
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * The price season
     *
     * @return BelongsTo
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(PriceSeason::class, 'price_season_id');
    }

    /**
     * The age group
     *
     * @return BelongsTo
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class, 'age_group', 'name');
    }
}
