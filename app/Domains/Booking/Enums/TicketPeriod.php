<?php

namespace App\Domains\Booking\Enums;

use ReflectionClass;
use App\Domains\Booking\Models\Ticket;
use BenSampo\Enum\Enum;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;
use function array_filter;
use function gettype;
use function in_array;
use function is_null;
use function stat;

final class TicketPeriod extends Enum
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

    public static function forTicket(Ticket $ticket)
    {
        if ($ticket->period === self::FLEX) {
            return self::FLEX;
        }

        if (is_null($ticket->home_flight_number)) {
            return self::ONEWAY;
        }

        return self::forDates(
            $ticket->outward_departure_datetime,
            $ticket->outward_arrival_datetime
        );
    }

    public static function forDates(DateTimeInterface $departsAt, DateTimeInterface $arrivesAt = null)
    {
        if (is_null($arrivesAt)) {
            return self::ONEWAY;
        }

        $departsAt = Date::instance($departsAt);
        $arrivesAt = Date::instance($arrivesAt);

        $diffInMonths = $departsAt->diffInMonths($arrivesAt);
        $diffInDays = $departsAt->addMonths($diffInMonths)->diffInDays($arrivesAt);

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

    public static function toSelectArray(): array
    {
        return [
            self::ONE_YEAR => static::getDescription(self::ONE_YEAR),
            self::ONE_MONTH => static::getDescription(self::ONE_MONTH),
            self::THREE_MONTHS => static::getDescription(self::THREE_MONTHS),
            self::ONEWAY => static::getDescription(self::ONEWAY),
            self::FLEX => static::getDescription(self::FLEX)
        ];
    }
}
