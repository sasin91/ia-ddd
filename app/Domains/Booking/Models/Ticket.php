<?php

namespace App\Domains\Booking\Models;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Billing\Models\Transaction;
use App\Domains\Booking\Contracts\Changeable;
use App\Domains\Booking\Models\Concerns\SendsDocuments;
use App\Domains\Booking\Models\Concerns\Serviceable;
use App\Domains\Booking\Models\Concerns\Voidable;
use App\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use function filled;

/**
 * Class Ticket
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
 * @property-read Collection $trips
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
class Ticket extends Model implements Changeable
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
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'PNR', 'PNR');
    }

    /**
     * Get the services attached to trips.
     * eg. Handicap support
     *
     * @return HasManyThrough
     */
    public function tripServices(): HasManyThrough
    {
        return $this->hasManyThrough(TripService::class, Trip::class);
    }

    /**
     * The prices of the booked tickets
     *
     * @return HasManyThrough
     */
    public function ticketPrices(): HasManyThrough
    {
        return $this->hasManyThrough(Price::class, Trip::class, 'PNR', 'PNR', 'id', 'price_id');
    }

    /**
     * Get all the ticket changes
     *
     * @return HasMany
     */
    public function ticketChanges(): HasMany
    {
        return $this->hasMany(TicketChange::class);
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

    /**
     * The transaction(s) made in relation to this booking
     *
     * @return MorphMany
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'product');
    }

    public function getSignedLinkAttribute(): string
    {
        return URL::signedRoute('tickets.show', ['ticket' => $this]);
    }

    public function isPurchased(): bool
    {
        return filled($this->purchased_at);
    }

    /**
     * Determine the fee for changing the model
     *
     * @param array $changes
     * @return int
     */
    public function determineChangeCost(array $changes): int
    {
        return $this->trips->sum(function (Trip $trip) use ($changes) {
            return $trip->determineChangeCost($changes);
        });
    }

    /**
     * Apply the changes to the model
     *
     * @param array $changes
     * @return $this
     */
    public function applyChanges(array $changes): Changeable
    {
        $this->trips->each->applyChanges($changes);

        return $this;
    }
}
