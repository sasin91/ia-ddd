<?php

namespace App\Domains\Booking\Models\Concerns;

use App\Domains\Booking\Mails\TicketDocuments;
use App\Domains\Booking\Mails\BookingInvoice;
use App\Domains\Booking\Models\Ticket;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

trait SendsDocuments
{
    /**
     * Update the documents sent at timestamp
     *
     * @return Ticket
     */
    public function markAsSent(): Ticket
    {
        return tap($this)->update([
            'documents_sent_at' => $this->freshTimestamp()
        ]);
    }

    /**
     * Get a signed URL to downloading the documents
     *
     * @return string
     */
    public function getSignedDocumentsLinkAttribute(): string
    {
        return URL::signedRoute('ticket.documents.download', ['ticket' => $this->getKey()]);
    }

    /**
     * Send the booking document to the buyer
     *
     * @return mixed
     */
    public function sendDocument()
    {
        return Mail::to($this->buyer_email)->queue(new TicketDocuments($this));
    }

    /**
     * Download the document PDF
     *
     * @return Response
     */
    public function downloadDocuments(): Response
    {
        return PDF::loadView('documents.booking-documents', [
            'ticket' => $this->loadMissing('trips', 'transactions')
        ])->download("IraqiAirways_document_{$this->PNR}.pdf");
    }

    /**
     * Get a signed URL to downloading the invoices
     *
     * @return string
     */
    public function getSignedInvoiceLinkAttribute(): string
    {
        return URL::signedRoute('booking.invoice.download', ['booking' => $this->getKey()]);
    }

    /**
     * Send the booking invoice to the buyer
     *
     * @return mixed
     */
    public function sendInvoice()
    {
        return Mail::to($this->buyer_email)->queue(new BookingInvoice($this));
    }

    /**
     * Download the invoice PDF
     *
     * @return Response
     */
    public function downloadInvoice(): Response
    {
        return PDF::loadView('documents.booking-invoice-pdf', [
            'ticket' => $this->loadMissing('trips', 'transactions')
        ])->download("IraqiAirways_invoice_{$this->PNR}.pdf");
    }
}
