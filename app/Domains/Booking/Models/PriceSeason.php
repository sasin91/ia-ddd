<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PriceSeason
 * @package App\Domains\Booking\Models
 *
 * @method static Builder coversTicket(Ticket $ticket)
 */
class PriceSeason extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * Query the seasons that covers given ticket
     *
     * @param Builder $query
     * @param Ticket $ticket
     * @return void
     */
    public function scopeCoversTicket($query, Ticket $ticket): void
    {
        $query->whereHas('dates', function ($query) use ($ticket) {
            /** @var Builder|PriceSeasonDate $query */
            $query->encompasses(
                $ticket->outward_departure_datetime,
                $ticket->outward_arrival_datetime
            );
        });
    }

    /**
     * The dates that compose this season
     *
     * @return HasMany
     */
    public function dates(): HasMany
    {
        return $this->hasMany(PriceSeasonDate::class, 'season_id');
    }
}
