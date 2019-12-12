<?php


namespace App\Domains\Booking\Controllers;


use App\Domains\Booking\Models\Travel;
use Inertia\Inertia;

class SelectAirports
{
    public function __invoke()
    {
        $travels = Travel::with('departureAirport', 'destinationAirport', 'stopovers.airport')->get();

        return Inertia::render('Booking/SelectAirports')
            ->with('departureAirports', $travels->pluck('departureAirport'))
            ->with('destinationAirports', $travels->pluck('destinationAirport')->merge($travels->flatMap->stopovers->map->airport->unique()));
    }
}