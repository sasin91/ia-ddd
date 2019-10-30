<?php


namespace App\Domains\Billing\Models;

use App\Domains\Agent\Events\MoneyRefundedToBalance;
use App\Domains\Agent\Events\PointsDepositedToAccount;
use App\Domains\Agent\Events\PointsRefundedToAccount;
use App\Domains\Agent\Events\PointsWithdrawnFromAccount;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Enums\PaymentCategory;
use App\Domains\Billing\Models\Concerns\CanBePaid;
use App\Domains\Billing\Models\Concerns\ResolvesBillingMethod;
use App\User;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use function event;
use DateTimeInterface;

/**
 * Class BillingMethod
 *
 * Money out of the company, eg. a Refund to a customer or a bill.
 *
 * @package App\Domains\BillingMethod\Models
 *
 * @property integer|null $account_id
 * @property integer|null $revenue_id
 * @property string $customer_email
 * @property integer $amount
 * @property integer $points
 * @property integer $exchange_rate
 * @property string $currency_code
 * @property string|null $description
 * @property PaymentCategory|string $category
 * @property string $billing_method
 * @property string|null $reference
 * @property DateTimeInterface|null $paid_at
 *
 * @property-read Account|null $account
 * @property-read Revenue|null $revenue Optional refunded revenue
 * @property-read User|null $customer
 */
class Payment extends Model
{
    use CanBePaid, CastsEnums, SoftDeletes, ResolvesBillingMethod;

    protected $fillable = [
        'account_id',
        'revenue_id',
        'customer_email',
        'amount',
        'points',
        'exchange_rate',
        'currency_code',
        'description',
        'category',
        'billing_method',
        'reference',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime'
    ];

    public $enumCasts = [
        'category' => PaymentCategory::class
    ];

    /**
     * Optionally, the account that's received the payment.
     * Typically when the category is a refund.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Optionally, the revenue that's been refunded.
     *
     * @return BelongsTo
     */
    public function revenue(): BelongsTo
    {
        return $this->belongsTo(Revenue::class, 'revenue_id');
    }


    /**
     * When an account is present, this is typically the Agent's User record.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_email', 'email');
    }

    /**
     * The transactions that's made in relation
     *
     * @return MorphMany
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transaction');
    }

    /**
     * Refund the full amount to the account ledger balance(s).
     *
     * @return void
     */
    public function refundToAccount(): void
    {
        event(
            new MoneyRefundedToBalance(
                $this->account->uuid,
                $this->amount,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Withdraw the earned points from the account.
     *
     * @return void
     */
    public function withdrawEarnedPointsFromAccount(): void
    {
        event(
            new PointsWithdrawnFromAccount(
                $this->account->uuid,
                $this->revenue->earned_points,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Withdrawn the points from the account.
     *
     * @return void
     */
    public function withdrawPointsFromAccount(): void
    {
        event(
            new PointsWithdrawnFromAccount(
                $this->account->uuid,
                $this->points,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Add the amount of points to the associated account.
     *
     * @return void
     */
    public function depositPointsToAccount(): void
    {
        event(
            new PointsDepositedToAccount(
                $this->account->uuid,
                $this->points,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Add the amount of points to the associated account.
     *
     * @return void
     */
    public function refundPointsToAccount(): void
    {
        event(
            new PointsRefundedToAccount(
                $this->account->uuid,
                $this->points,
                static::class,
                $this->getKey()
            )
        );
    }
}
