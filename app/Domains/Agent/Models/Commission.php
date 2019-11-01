<?php

namespace App\Domains\Agent\Models;

use App\Domains\Billing\Models\Concerns\Discountable;
use App\Domains\Booking\Models\Travel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use Discountable, SoftDeletes;

    protected $fillable = [
        'account_id',
        'travel_id',
        'base',
        'extra',
        'points_percentage'
    ];

    protected $casts = [
        'base' => 'integer',
        'extra' => 'double',
        'points_percentage' => 'double'
    ];

    /**
     * The agent account that has the commission(s)
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The travel the commission is valid for
     *
     * @return BelongsTo
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }
}
