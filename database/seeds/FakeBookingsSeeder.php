<?php

use App\Domains\Aero\Models\Aero;
use App\Domains\Aero\Models\AeroAction;
use App\Domains\Agent\Models\Commission;
use App\Domains\Billing\ExchangeRate;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Billing\Models\Transaction;
use App\Domains\Booking\Enums\TripType;
use App\Domains\Booking\Events\BookTickets;
use App\Domains\Booking\Models\Ticket;
use App\Domains\Booking\Models\Trip;
use App\Domains\Booking\Models\Price;
use App\Domains\Booking\Models\Travel;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FakeBookingsSeeder extends Seeder
{
    use WithFaker;

    protected $outwardTravel;
    protected $homeTravel;

    public function __construct()
    {
        $this->setUpFaker();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ExchangeRate::fake(
            array_map(function () {
                return 1;
            }, array_flip(ExchangeRate::currencyPairs()))
        );

        $this->outwardTravel = factory(Travel::class)->create(['flight_number' => 'FAKE1']);
        $this->homeTravel = factory(Travel::class)->create(['flight_number' => 'FAKE2']);

        $this->createOnlineTickets(5);
        $this->createAgentTickets(5);
        $this->createStaffTickets(3);

        $aero = Aero::query()->firstOr(function () {
            return factory(Aero::class)->create();
        });

        foreach (Trip::query()->cursor() as $trip) {
            $rng = rand(0, 9);

            if ($rng >= 5) {
                factory(AeroAction::class)->create([
                    'aero_id' => $aero->id,
                    'trip_id' => $trip->id
                ]);
            }
        }
    }

    private function createOnlineTickets(int $count)
    {
        for ($i=0; $i < $count; $i++) {
            $PNR = Str::random(5);

            $ticket = [
                'PNR' => $PNR,
                'buyer_email' => $this->faker()->safeEmail,
            ];

            $trips = [];
            for ($i=0; $i < rand(1, 5); $i++) {
                $trips[] = $this->tripAttributes([
                    'PNR' => $PNR,
                    'outward_travel' => $this->outwardTravel->flight_number,
                    'outward_departure_datetime' => $outwardDate = $this->faker()->dateTimeThisMonth,
                    'home_departure_datetime' => $homeDate = $this->faker()->dateTimeBetween($outwardDate),
                    'home_travel' => $this->homeTravel->flight_number,
                    'type' => TripType::forDates($outwardDate, $homeDate)
                ]);
            }

            event(new BookTickets(
                $ticket,
                $trips,
                factory(Revenue::class)->create()->id
            ));
        }
    }

    private function createAgentTickets(int $count)
    {
        $agent = factory(User::class)->state('agent')->create();
        $account = $agent->accounts->first();
        $account->deposit(rand(1000, 100000));

        for ($i=0; $i < $count; $i++) {
            $PNR = Str::random(5);

            $ticket = [
                'PNR' => $PNR,
                'buyer_email' => $this->faker()->safeEmail,
                'buyer_id' => $agent->id
            ];

            $trips = [];
            for ($i=0; $i < rand(1, 5); $i++) {
                $trips[] = $this->tripAttributes([
                    'PNR' => $PNR,
                    'outward_travel' => $this->outwardTravel->flight_number,
                    'type' => TripType::ONEWAY
                ]);
            }

            event(new BookTickets(
                $ticket,
                $trips,
                $account->withdraw(rand(500, 3000))->id
            ));
        }
    }

    private function createStaffTickets(int $count)
    {
        $receptionist = tap(factory(User::class)->create())->assignRole('Receptionist');

        for ($i=0; $i < $count; $i++) {
            $PNR = Str::random(5);

            $ticket = [
                'PNR' => $PNR,
                'buyer_email' => $this->faker()->safeEmail,
                'buyer_id' => $receptionist->id
            ];

            $trips = [];
            for ($i=0; $i < rand(1, 5); $i++) {
                $trips[] = $this->tripAttributes([
                    'PNR' => $PNR,
                    'outward_travel' => $this->outwardTravel->flight_number,
                    'type' => TripType::ONEWAY
                ]);
            }

            event(new BookTickets(
                $ticket,
                $trips,
                factory(Revenue::class)->create()->id
            ));
        }
    }

    private function tripAttributes(array $overrides = [])
    {
        return Arr::except(
            factory(Trip::class)->make($overrides)->toArray(),
            'price'
        );
    }
}
