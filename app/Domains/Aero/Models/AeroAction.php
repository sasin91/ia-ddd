<?php

namespace App\Domains\Aero\Models;

use App\Domains\Booking\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AeroAction
 * @package App
 *
 * @method static Builder unconfirmed()
 * @method static Builder confirmed()
 *
 * @property Ticket $ticket
 */
class AeroAction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'aero_id',
        'command',

        'e_ticket',
        'tax',
        'price'
    ];

    /**
     * The trip the action refers to
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
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
