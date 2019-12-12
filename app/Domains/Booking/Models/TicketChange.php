<?php

namespace App\Domains\Booking\Models;

use App\Domains\Booking\Enums\TicketChangeStatus;
use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use function tap;

/**
 * Class Change
 *
 * @method static Builder confirmed()
 * @method static Builder unconfirmed()
 * @method static Builder completed()
 * @method static Builder pending()
 */
class TicketChange extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'handled_by',
        'requested_by',
        'ticket_id',

        'fee',
        'cost',
        'diff',

        'before',
        'after',

        'confirmed_at',
        'completed_at'
    ];

    protected $casts = [
        'fee' => 'integer',
        'cost' => 'integer',
        'diff' => 'integer',

        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the confirmed modifications
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeConfirmed($query)
    {
        return $query
            ->withoutGlobalScope('unconfirmed')
            ->whereNotNull('confirmed_at');
    }

    /**
     * Get the unconfirmed modifications
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeUnconfirmed($query)
    {
        return $query
            ->withoutGlobalScope('confirmed')
            ->whereNull('confirmed_at');
    }

    /**
     *
     * Get the completed changes
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeCompleted($query)
    {
        return $query
            ->withoutGlobalScope('pending')
            ->whereNotNull('completed_at');
    }

    /**
     * Get the changes that has not yet been completed.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePending($query)
    {
        return $query
            ->withoutGlobalScope('completed')
            ->whereNull('completed_at');
    }

    /**
     * The ticket being changed
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * An optional trip.
     * No trip means affecting entire ticket.
     *
     * @return BelongsTo
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * The staff member that carries out the change
     *
     * @return BelongsTo
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * The optional User or Agent that requested the change
     *
     * @return BelongsTo
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function getStatusAttribute(): string
    {
        return TicketChangeStatus::forModel($this);
    }

    /**
     * Apply the changes
     *
     * @return Trip
     */
    public function apply(): Trip
    {
        return tap($this->ticket->applyChanges(), function () {
            $this->markAsCompleted();
        });
    }

    /**
     * Mark the ticket order change as completed
     *
     * @param DateTime|string|null $atDate
     * @return $this
     */
    public function markAsCompleted($atDate = null)
    {
        return tap($this)->update(['completed_at' => Carbon::parse($atDate)->format($this->getDateFormat())]);
    }

    /**
     * Mark the ticket order change as confirmed
     *
     * @param DateTime|string|null $atDate
     * @return $this
     */
    public function markAsConfirmed($atDate = null)
    {
        return tap($this)->update(['confirmed_at' => Carbon::parse($atDate)->format($this->getDateFormat())]);
    }
}
