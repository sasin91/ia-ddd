<?php

namespace App\Domains\Booking\Mails;

use App\Domains\Booking\Models\Booking;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingDocuments extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Booking
     */
    public $booking;

    /**
     * BookingDocumentsMail constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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

        $this->booking->markAsSent();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->booking->loadMissing([
            'mainTrips',
            'mainTrips.services',
            'mainTrips.aeroActions' => function ($q) { $q->where('type', AeroActionType::ADDED)->take(1); },
            'mainTrips.homeTrip',
            'mainTrips.travelDate',
            'mainTrips.travelDate.travel',
            'mainTrips.travelDate.travel.flight'
        ]);

        $PDF = PDF::loadView('documents.booking-documents', [
            'booking' => $this->booking
        ]);

        $PDF->setPaper('A4', 'landscape');

        return $this->view('mails.booking-documents', [
            'booking' => $this->booking
        ])->attachData($PDF->output(), "IraqiAirways_documents_{$this->booking->PNR}.pdf", [
            'mime' => 'application/pdf',
        ])
            ->subject(__(
                'Iraqi Airways Travel documents :PNR',
                ['PNR' => $this->booking->PNR],
                optional($this->booking->buyer)->locale ?? app()->getLocale()
            ));
    }
}
