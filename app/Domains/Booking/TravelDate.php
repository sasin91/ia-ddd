<?php

namespace App\Domains\Booking;

use App\Domains\Booking\Collections\TravelDateCollection;
use App\Domains\Booking\Models\Travel;
use App\Domains\Booking\Models\TravelStopover;
use App\Support\DateRange;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class TravelDate
{
    public static function range($fromDate, $toDate, Travel $travel)
    {
        return new TravelDateCollection(
            DateRange::collect($fromDate, $toDate)
                ->whereWeekdayIn($travel->times->pluck('weekday')->unique())
                ->map(function ($date) use ($travel) {
                    $weekday = Date::parse($date)->format('l');

                    $times = $travel->times->firstWhere('weekday', $weekday);

                    $departsAt = TravelDate::make(
                        Date::parse($date)->setTimeFromTimeString($times->departure_time),
                        $travel->departureAirport->timezone
                    );

                    $arrivesAt = TravelDate::make(
                        Date::parse($date)->setTimeFromTimeString($times->arrival_time),
                        $travel->destinationAirport->timezone
                    );

                    // +1 day if the departure & arrival day is equal and arrival is past midnight
                    if ($departsAt->format('Y-m-d') === $arrivesAt->format('Y-m-d')) {
                        if (Str::startsWith($arrivesAt->format('H'), '0')) {
                            $arrivesAt->addDay();
                        }
                    }

                    $dates = [];
                    $dates[] = $departsAt;
                    $travel->stopovers->where('weekday', $weekday)->each(function (TravelStopover $stopover) use (&$dates, $date) {
                        $arrivesAt = TravelDate::make(
                            Date::parse($date)->setTimeFromTimeString($stopover->arrival_time),
                            $stopover->airport->timezone
                        );

                        $departsAt = TravelDate::make(
                            Date::parse($date)->setTimeFromTimeString($stopover->departure_time),
                            $stopover->airport->timezone
                        );

                        if ($departsAt->format('Y-m-d') === $arrivesAt->format('Y-m-d')) {
                            if (Str::startsWith($departsAt->format('H'), '0')) {
                                $departsAt->addDay();
                            }
                        }

                        $dates[] = $arrivesAt;
                        $dates[] = $departsAt;
                    });
                    $dates[] = $arrivesAt;

                    return $dates;
                })
                ->reject(function (array $dates) use ($travel) {
                    return $travel->cancels->contains->affects($dates[0]);
                })
                ->reject(function (array $dates) use ($travel) {
                    return $travel->seats->contains(function ($seat) use ($dates) {
                        return $seat->remaining <= 0
                            && $dates[0]->equalTo($seat->departs_at);
                    });
                })
                ->each(function (array $dates) use ($travel) {
                    $changes = $travel->changes->filter->affects($dates[0]);

                    foreach ($dates as $date) {
                        $changes->each->apply($date);
                    }
                })
                ->values()
        );
    }

    public static function make($time, $timezone)
    {
        return new Carbon($time, $timezone);
    }
}

