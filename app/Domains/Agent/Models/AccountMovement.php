<?php


namespace App\Domains\Agent\Models;


use App\StoredEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_ledger_id',
        'stored_event_id',
        'causer_type',
        'causer_id',
        'amount',
        'exchange_rate'
    ];

    /**
     * The account ledger that's affected by this movement
     *
     * @return BelongsTo
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class);
    }

    /**
     * The event that influenced the movement
     *
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(StoredEvent::class, 'stored_event_id');
    }

    /**
     * The model that caused this movement
     *
     * @return MorphTo
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
