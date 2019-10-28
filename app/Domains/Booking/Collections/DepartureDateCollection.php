<?php

namespace App\Domains\Booking\Collections;

use Illuminate\Support\Collection;
use function array_map;
use DateTimeInterface;

class TravelDateCollection extends Collection
{
    public function format(string $dateFormat = 'Y-m-d H:i:s')
    {
        return $this->map(function (array $dates) use ($dateFormat) {
            return array_map(function (DateTimeInterface $date) use ($dateFormat) {
                return $date->format($dateFormat);
            }, $dates);
        });
    }
}
