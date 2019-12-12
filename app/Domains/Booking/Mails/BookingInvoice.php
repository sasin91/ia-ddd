<?php

namespace App\Domains\Booking\Mails;

use App\Domains\Booking\Models\Ticket;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingInvoice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->onQueue('mails');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $PDF = PDF::loadView('documents.ticket-invoice-pdf', [
            'ticket' => $this->ticket->loadMissing('tickets', 'transactions')
        ]);

        return $this
            ->view('mails.ticket-invoice', ['ticket' => $this->ticket])
            ->attachData($PDF->output(), "IraqiAirways_invoice_{$this->ticket->PNR}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->subject("Iraqi Airways Invoice {$this->ticket->PNR}");
    }
}
