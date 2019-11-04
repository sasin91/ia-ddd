<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Locale;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use function blank;
use function head;

/**
 * Class Airport
 * @package App\Domains\Booking\Models
 *
 * @property string $IATA
 * @property string $timezone
 * @property string $location
 * @property string $country
 */
class Airport extends Model
{
    protected $fillable = [
        'timezone',
        'location',
        'country',
        'IATA',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function (Airport $airport) {
            if (blank($airport->timezone)) {
                $airport->timezone = head(
                    DateTimeZone::listIdentifiers(
                        DateTimeZone::PER_COUNTRY,
                        $airport->country
                    )
                );
            }
        });
    }

    public function getLocationAttribute($value)
    {
        return __($value);
    }

    public function departures(): HasMany
    {
        return $this->hasMany(Travel::class, 'departure_airport_id');
    }

    public function arrivals(): HasMany
    {
        return $this->hasMany(Travel::class, 'destination_airport_id');
    }
}
