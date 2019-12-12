<?php

namespace App\Domains\Booking\Events;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;
use Spatie\EventSourcing\ShouldBeStored;
use function head;

class BookTickets implements ShouldBeStored
{
    /**
     * Booking Attributes
     * PNR,buyer_email,buyer_id,express
     *
     * @var array
     */
    public $ticket = [];

    /**
     * The ticket attributes.
     * [[flight_number, dates, passenger_id and so on.]]
     *
     * @var array<array>
     */
    public $trips = [];

    /**
     * ID of the revenue that's made for this booking
     *
     * @var integer|null
     */
    public $revenueId;

    public function __construct(array $ticket, array $trips, int $revenueId)
    {
        $this->ticket = $ticket;
        $this->trips = $trips;
        $this->revenueId = $revenueId;
    }
}
