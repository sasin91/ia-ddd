<?php


namespace App\Domains\Agent\Models;

use Exchanger\Contract\ExchangeRate;
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

    public function getCurrencyPairAttribute(): string
    {
        $defaultCurrency = config('currency.default');

        return "{$defaultCurrency}/{$this->currency}";
    }

    public function exchangeRate(): ExchangeRate
    {
        if ($this->asDateTime($this->created_at)->isToday()) {
            return Swap::latest($this->currency_pair);
        }

        return Swap::historical(
            $this->currency_pair,
            $this->created_at
        );
    }
}
