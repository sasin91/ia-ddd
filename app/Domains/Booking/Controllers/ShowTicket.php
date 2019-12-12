<?php

namespace App\Domains\Booking\Controllers;

use App\Domains\Booking\Models\Ticket;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ShowTicket
{
    public function __invoke(Ticket $booking)
    {
        abort_unless(Request::hasValidSignature(), 401, 'Invalid Booking Signature.');

        return Inertia::render('ShowTicket')->with('ticket', $booking);
    }
}
