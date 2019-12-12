<?php

namespace App\Domains\Booking\Models\Concerns;

use App\Domains\Booking\Models\Ticket;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

trait Voidable
{
    /**
     * Get the voided bookings
     *
     * @param Builder $query
     * @param DateTimeInterface|string|null $date
     *
     * @return void
     */
    public function scopeVoided($query, $date = null): void
    {
        $query->withoutGlobalScope('unvoided');

        if ($date) {
            $query
                ->where('voided_at', '>=', $this->asDateTime($date)->startOfDay())
                ->where('voided_at', '<=', $this->asDateTime($date)->endOfDay());
        } else {
            $query->whereNotNull('voided_at');
        }
    }

    /**
     * Get the tickets that have not been voided.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeUnvoided($query): void
    {
        $query->withoutGlobalScope('voided');

        $query->whereNull('voided_at');
    }

    /**
     * Mark the tickets as voided.
     *
     * @return Ticket
     */
    public function markAsVoided(): Ticket
    {
        return tap($this)->update(['voided_at' => $this->freshTimestamp()]);
    }
}
