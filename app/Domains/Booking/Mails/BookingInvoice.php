<?php

namespace App\Domains\Booking\Mails;

use App\Domains\Booking\Models\Booking;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingInvoice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->onQueue('mails');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $PDF = PDF::loadView('documents.booking-invoice-pdf', [
            'booking' => $this->booking->loadMissing('tickets', 'transactions')
        ]);

        return $this
            ->view('mails.booking-invoice', ['booking' => $this->booking])
            ->attachData($PDF->output(), "IraqiAirways_invoice_{$this->booking->PNR}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->subject("Iraqi Airways Invoice {$this->booking->PNR}");
    }
}
