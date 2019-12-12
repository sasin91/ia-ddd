<?php


namespace App\Domains\Booking\Controllers;


use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Domains\Booking\Enums\PassengerGender;
use App\Domains\Booking\Enums\PassengerTitle;
use App\Domains\Booking\Enums\TripType;
use App\Domains\Booking\Models\AgeGroup;
use App\Domains\Booking\Models\Travel;
use Inertia\Inertia;

class SelectTravels
{
    public function __invoke(string $departure, string $destination)
    {
        // Times, Changes, Cancels are used for generating the available_timestamps
        $outwardTravels = Travel::with('times', 'changes', 'cancels', 'stopovers')->where('departure_airport', $departure)->get();
        $homeTravels = Travel::with('times', 'changes', 'cancels', 'stopovers')
            ->where('departure_airport', $destination)
            ->orWhereHas('stopovers', function ($stopovers) use ($destination) {
                $stopovers->where('airport_IATA', $destination);
            })
            ->get();

        return Inertia::render('Booking/SelectTravels')
            ->with('departure', $departure)
            ->with('destination', $destination)

            ->with('travelPeriods', TripType::toSelectArray())
            ->with('outwardTravels', $outwardTravels->each->append('available_timestamps')->values())
            ->with('homeTravels', $homeTravels->each->append('available_timestamps')->values())

            ->with('ageGroups', AgeGroup::query()->orderBy('name')->get(['name', 'passenger_limit', 'from', 'to']))
            ->with('passengerTitles', PassengerTitle::toSelectArray())
            ->with('passengerGenders', PassengerGender::toSelectArray())
            ->with('passengerNationalities', Nationality::toSelectArray())
            ->with('passengerCitizenships', Citizenship::toSelectArray());
    }
}