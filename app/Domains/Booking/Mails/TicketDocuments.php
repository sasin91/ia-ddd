<?php

namespace App\Domains\Booking\Mails;

use App\Domains\Booking\Models\Ticket;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketDocuments extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * TicketDocuments constructor.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->onQueue('mails');
    }

    /**
     * Send the message using the given mailer.
     *
     * @param  Mailer $mailer
     * @return void
     */
    public function send(Mailer $mailer)
    {
        parent::send($mailer);

        $this->ticket->markAsSent();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->ticket->loadMissing([
            'mainTrips',
            'mainTrips.services',
            'mainTrips.aeroActions' => function ($q) { $q->where('type', AeroActionType::ADDED)->take(1); },
            'mainTrips.homeTrip',
            'mainTrips.travelDate',
            'mainTrips.travelDate.travel',
            'mainTrips.travelDate.travel.flight'
        ]);

        $PDF = PDF::loadView('documents.ticket-documents', [
            'ticket' => $this->ticket
        ]);

        $PDF->setPaper('A4', 'landscape');

        return $this->view('mails.ticket-documents', [
            'ticket' => $this->ticket
        ])->attachData($PDF->output(), "IraqiAirways_documents_{$this->ticket->PNR}.pdf", [
            'mime' => 'application/pdf',
        ])
            ->subject(__(
                'Iraqi Airways Ticket Documents :PNR',
                ['PNR' => $this->ticket->PNR],
                optional($this->ticket->buyer)->locale ?? app()->getLocale()
            ));
    }
}
