<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTimeInterface;

/**
 * Class PriceSeasonDate
 * @package App\Domains\Booking\Models
 *
 * @method static Builder|PriceSeasonDate encompasses($departure, $arrival)
 */
class PriceSeasonDate extends Model
{
    protected $fillable = ['season_id', 'starts_at', 'ends_at'];

    protected $dates = ['starts_at', 'ends_at'];

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
     * Query seasons dates that covers given dates.
     *
     * @param Builder|PriceSeasonDate $query
     * @param DateTimeInterface|string $departure
     * @param DateTimeInterface|string $arrival
     * @return void
     */
    public function scopeEncompasses($query, $departure, $arrival): void
    {
        $query
            ->where('starts_at', '>=', $this->asDateTime($departure)->startOfDay())
            ->where('ends_at', '<=', $this->asDateTime($arrival)->endOfDay());
    }

    public function covers($date)
    {
        return Carbon::parse($date)->between($this->starts_at, $this->ends_at);
    }
}
