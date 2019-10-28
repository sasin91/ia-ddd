<?php

namespace App\Domains\Booking\Models;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Booking\Models\Concerns\SendsDocuments;
use App\Domains\Booking\Models\Concerns\Serviceable;
use App\Domains\Booking\Models\Concerns\Voidable;
use App\Domains\Booking\Tickets\TicketChange;
use App\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use function filled;

/**
 * Class Booking
 * @note PNR aka. Booking reference
 *
 * @property string $PNR
 * @property string $buyer_email
 * @property integer $buyer_id
 * @property boolean $express
 * @property integer $total_cost
 * @property integer $transaction_id
 * @property DateTimeInterface|null $voided_at
 * @property DateTimeInterface|null $documents_sent_at
 * @property-read Collection $tickets
 * @property-read Collection $ticketServices
 * @property-read Collection $ticketPrices
 * @property-read Collection $ticketChanges
 * @property-read User|null $buyer
 * @property-read Invoice|null $transaction
 *
 * @method static Builder express(bool $value = true)
 * @method static Builder reserved($date = null)
 * @method static Builder purchased($date = null)
 * @method static Builder voided($date = null)
 * @method static Builder unvoided($date = null)
 */
class Booking extends Model
{
    use Serviceable, SendsDocuments, SoftDeletes, Voidable;

    protected $fillable = [
        'PNR',
        'buyer_email',
        'buyer_id',
        'express',
        'total_cost',
        'voided_at',
        'documents_sent_at'
    ];

    protected $casts = [
        'buyer_id' => 'integer',
        'express' => 'boolean',
        'voided_at' => 'datetime',
        'documents_sent_at' => 'datetime'
    ];

    /**
     * Get the purchased bookings
     *
     * @param Builder $query
     * @param string|DateTimeInterface|null $date
     * @return Builder
     */
    public function scopePurchased($query, $date = null)
    {
        $query->withoutGlobalScope('reserved');

        if ($date) {
            $query->whereDate('purchased_at', $date);
        }

        return $query->whereNotNull('purchased_at');
    }

    /**
     * Get the refunded bookings
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRefunded($query)
    {
        return $query->has('refunds');
    }

    /**
     * The booked tickets.
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'booking_id');
    }

    /**
     * Get the services attached to tickets.
     * eg. Handicap support
     *
     * @return HasManyThrough
     */
    public function ticketServices(): HasManyThrough
    {
        return $this->hasManyThrough(TicketService::class, Ticket::class);
    }

    /**
     * The prices of the booked tickets
     *
     * @return HasManyThrough
     */
    public function ticketPrices(): HasManyThrough
    {
        return $this->hasManyThrough(TicketPrice::class, Ticket::class, 'booking_id', 'id', 'id', 'price_id');
    }

    /**
     * Get all the ticket changes
     *
     * @return HasManyThrough
     */
    public function ticketChanges(): HasManyThrough
    {
        return $this->hasManyThrough(TicketChange::class, Ticket::class);
    }

    /**
     * The buyer, typically an agent user.
     *
     * @return BelongsTo
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getSignedLinkAttribute(): string
    {
        return URL::signedRoute('booking.show', ['booking' => $this]);
    }

    public function isPurchased(): bool
    {
        return filled($this->purchased_at);
    }
}
