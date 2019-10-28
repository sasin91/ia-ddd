<?php

namespace App\Collections;

use function in_array;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class DateRangeCollection extends Collection
{
    /**
     * Filter the dates by given weekdays
     *
     * @param   array|Collection|string  $weekdays
     *
     * @return  DateRangeCollection
     */
    public function whereWeekdayIn($weekdays)
    {
        $weekdays = is_array($weekdays) ? $weekdays : collect($weekdays)->toArray();

        return $this->filter(function ($date) use ($weekdays) {
            return in_array(
                Date::parse($date)->format('l'),
                $weekdays
            );
        });
    }
}
