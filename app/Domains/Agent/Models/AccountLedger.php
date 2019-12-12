<?php


namespace App\Domains\Agent\Models;

use App\Domains\Billing\Contracts\ExchangeRatePair;
use App\Domains\Billing\ExchangeRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Swap\Laravel\Facades\Swap;
use function config;

class AccountLedger extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'currency',
        'balance',
    ];

    /**
     * The parent account
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * The account movements, the "same" movement appears multiple times per currency / exchange rate.
     *
     * @return HasMany
     */
    public function movements(): HasMany
    {
        return $this->hasMany(AccountMovement::class);
    }

    /**
     * Get the viable exchange rate for the ledger
     *
     * @return ExchangeRatePair
     */
    public function exchangeRate(): ExchangeRatePair
    {
        return ExchangeRate::find(
            $this->currency,
            config('currency.default'),
            $this->created_at
        );
    }
}
