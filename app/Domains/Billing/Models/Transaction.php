<?php

namespace App\Domains\Billing\Models;

use App\Domains\Booking\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'transaction_id',

        'product_type',
        'product_id'
    ];

    /**
     * The actual transaction
     *
     * @return MorphTo<Revenue,Expense>
     */
    public function transaction(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The product the transaction is made for
     *
     * @return BelongsTo<Ticket>
     */
    public function product(): BelongsTo
    {
        return $this->morphTo();
    }
}
