<?php

namespace App\Domains\Booking\Projectors;

use App\Domains\Aero\Models\AeroAction;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Billing\Models\Transaction;
use App\Domains\Booking\Events\BookTickets;
use App\Domains\Booking\Models\Trip;
use App\Domains\Booking\Models\TicketChange;
use App\Domains\Booking\Models\TripService;
use App\Domains\Booking\Models\Ticket;
use App\Domains\Booking\Models\Passenger;
use App\Domains\Booking\Models\Price;
use App\Domains\Economy\Models\ReportLine;
use Illuminate\Support\Arr;
use Spatie\EventSourcing\Facades\Projectionist;
use Spatie\EventSourcing\Projectors\Projector;
use Spatie\EventSourcing\Projectors\ProjectsEvents;
use Spatie\EventSourcing\Projectors\QueuedProjector;
use Spatie\EventSourcing\StoredEvent;
use Spatie\EventSourcing\StoredEventRepository;

class BookingProjector implements QueuedProjector
{
    use ProjectsEvents;

    /*
     * Here you can specify which event should trigger which method.
     */
    protected $handlesEvents = [
        BookTickets::class
    ];

    public function resetState()
    {
        AeroAction::truncate();
        TicketChange::truncate();
        TripService::truncate();
        ReportLine::truncate();
        Trip::truncate();
        Ticket::truncate();
    }

    public function onBookTickets(BookTickets $event, StoredEvent $storedEvent, StoredEventRepository $repository)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::query()->create($event->ticket);

        foreach ($event->trips as $tripAttributes) {
            $passengerAttributes = Arr::pull($tripAttributes, 'passenger');

            $trips = new Trip($tripAttributes);

            if ($passengerAttributes) {
                $trips->passenger()->associate(
                    array_key_exists('id', $passengerAttributes)
                        ? Passenger::query()->find($passengerAttributes['id'])
                        : Passenger::query()->create($passengerAttributes)
                );
            }

            $ticket->trips()->save($trips);
        }

        $ticket->update(['total_cost' => $ticket->tickets->sum('price.amount')]);

        if ($event->revenueId) {
            $ticket->transactions()->create([
                'transaction_type' => Revenue::class,
                'transaction_id' => $event->revenueId
            ]);
        }

        if (Projectionist::isReplaying() === false) {
            $storedEvent->meta_data->put('links.booking', $ticket->signed_link);
            $repository->update($storedEvent);
        }
    }
}
