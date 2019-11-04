<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TicketPrice
 * @package App\Domains\Booking\Models
 *
 * @method static Builder forOutwardTicket(Ticket $ticket)
 * @method static Builder forHomeTicket(Ticket $ticket)
 * @method static Builder forTicket(Ticket $ticket)
 */
class TicketPrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'travel_id',
        'price_season_id',
        'ticket_period',
        'age_group_id',
        'currency',
        'amount'
    ];

    /**
     * Get the travel prices that matches given ticket
     *
     * @param Builder|TicketPrice $query
     * @param Ticket $ticket
     * @return void
     */
    public function scopeForOutwardTicket($query, Ticket $ticket): void
    {
        $query
            ->forTicket($ticket)
            ->where('travel_id', $ticket->outwardTravel->id);
    }

    /**
     * Get the travel prices that matches given ticket
     *
     * @param Builder|TicketPrice $query
     * @param Ticket $ticket
     * @return void
     */
    public function scopeForHomeTicket($query, Ticket $ticket): void
    {
        $query
            ->forTicket($ticket)
            ->where('travel_id', $ticket->homeTravel->id);
    }

    /**
     * Get the travel prices that matches given ticket
     *
     * @param Builder|TicketPrice $query
     * @param Ticket $ticket
     * @return void
     */
    public function scopeForTicket($query, Ticket $ticket): void
    {
        $query
            ->where('ticket_period', $ticket->period)
            ->where('age_group_id', $ticket->passenger->age_group_id)
            ->whereHas('season', function ($season) use ($ticket) {
                /** @var Builder|PriceSeason $season */
                $season->coversTicket($ticket);
            });
    }

    /**
     * The travel this price is associated with
     *
     * @return BelongsTo
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * The price season
     *
     * @return BelongsTo
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(PriceSeason::class);
    }

    /**
     * The age group
     *
     * @return BelongsTo
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class);
    }
}
