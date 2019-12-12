<?php

namespace App\Domains\Booking\Enums;

use App\Domains\Booking\Models\Trip;
use BenSampo\Enum\Enum;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;
use ReflectionClass;

use function in_array;
use function is_null;

final class TripType extends Enum
{
    const ONEWAY = 'one-way';
    const FLEX   = 'flex';
    const ONE_MONTH = '1m';
    const THREE_MONTHS = '3m';
    const ONE_YEAR = '1y';

    const REQUIRES_HOME = [
        self::ONE_YEAR,
        self::ONE_MONTH,
        self::THREE_MONTHS,
        self::FLEX
    ];

    const DAYS = [
        self::ONEWAY => 0,
        self::FLEX  =>  0,
        self::ONE_MONTH => 30,
        self::THREE_MONTHS => 90,
        self::ONE_YEAR => '> 90',
    ];

    public static function forTrip(Trip $trip)
    {
        if ($trip->type === self::FLEX) {
            return self::FLEX;
        }

        if (is_null($trip->home_travel)) {
            return self::ONEWAY;
        }

        return self::forDates(
            $trip->outward_departure_datetime,
            $trip->outward_arrival_datetime
        );
    }

    public static function forDates(DateTimeInterface $outwardDepartureDate, DateTimeInterface $homeDepartureDate = null)
    {
        if (is_null($homeDepartureDate)) {
            return self::ONEWAY;
        }

        $outwardDepartureDate = Date::instance($outwardDepartureDate);
        $homeDepartureDate = Date::instance($homeDepartureDate);

        $diffInMonths = $outwardDepartureDate->diffInMonths($homeDepartureDate);
        $diffInDays = $outwardDepartureDate->addMonths($diffInMonths)->diffInDays($homeDepartureDate);

        return self::forMonths($diffInMonths, $diffInDays);
    }

    public static function forMonths(int $months, int $days = null)
    {
        if ($months === 0 || $months === 1) {
            if ($months === 1 && ($days && $days > 1)) {
                return self::THREE_MONTHS;
            }

            return self::ONE_MONTH;
        }

        if ($months === 2 || $months === 3) {
            if ($months === 3 && ($days && $days > 1)) {
                return self::ONE_YEAR;
            }

            return self::THREE_MONTHS;
        }

        if ($months > 3) {
            return self::ONE_YEAR;
        }

        return self::ONEWAY;
    }

    public static function requiresHome($period): bool
    {
        return in_array($period, static::REQUIRES_HOME);
    }

    /**
     * Get all of the constants defined on the class.
     *
     * @return array
     */
    protected static function getConstants(): array
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, static::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            static::$constCacheArray[$calledClass] = array_filter($reflect->getConstants(), function ($reflectionConstant) {
                return is_scalar($reflectionConstant);
            });
        }

        return static::$constCacheArray[$calledClass];
    }
}
