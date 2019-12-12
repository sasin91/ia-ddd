<?php

namespace App\Domains\Aero\Models;

use DateTimeInterface;
use BenSampo\Enum\Traits\CastsEnums;
use App\Domains\Booking\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Domains\Aero\Enums\AeroActionType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AeroAction
 * @package App
 *
 * @method static Builder executed()
 * @method static Builder pending()
 *
 * @property Trip $ticket
 */
class AeroAction extends Model
{
    use CastsEnums, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'aero_id',
        'command',
        'type',
        'e_ticket',
        'tax',
        'price',
        'executed_at'
    ];

    public $enumCasts = [
        'type' => AeroActionType::class
    ];

    /**
     * Query the actions that has been executed by the RPA
     *
     * @param Builder $query
     * @param null|string|DateTimeInterface $atDate
     * @return void
     */
    public function scopeExecuted($query, $atDate = null): void
    {
        $query->withoutGlobalScope('pending');

        if ($atDate) {
            $query
                ->where('executed_at', '>=', $this->asDateTime($atDate)->startOfDay())
                ->where('executed_at', '<=', $this->asDateTime($atDate)->endOfDay());
        } else {
            $query->whereNotNull('executed_at');
        }
    }

    /**
     * Query the actions pending executing by the RPA
     *
     * @param Builder $query
     * @return void
     */
    public function scopePending($query): void
    {
        $query->withoutGlobalScope('executed');

        $query->whereNull('executed_at');
    }

    /**
     * Mark the aero action as executed
     *
     * @return $this
     */
    public function markAsExecuted()
    {
        $this->update(['executed_at' => $this->freshTimestamp()]);

        return $this;
    }

    /**
     * The trip the action refers to
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * The aero provider / terminal
     *
     * @return BelongsTo
     */
    public function aero(): BelongsTo
    {
        return $this->belongsTo(Aero::class);
    }
}
