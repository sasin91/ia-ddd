<?php


namespace App\Domains\Billing\Models;

use App\Domains\Agent\Events\MoneyDepositedToBalance;
use App\Domains\Agent\Events\MoneyWithdrawnFromBalance;
use App\Domains\Agent\Events\PointsEarned;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\Events\RevenueRefunded;
use App\Domains\Billing\Events\RevenueRefunding;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Models\Concerns\CanBePaid;
use App\Domains\Billing\Models\Concerns\HasDiscounts;
use App\Domains\Billing\Models\Concerns\ResolvesBillingMethod;
use App\User;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;
use function blank;
use function event;
use function points_for;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class Revenue
 *
 * Money in to the company, eg. a purchased ticket.
 *
 * @package App\Domains\BillingMethod\Models
 * @property integer|null $account_id
 * @property string $customer_email
 * @property integer $amount
 * @property integer $earned_points
 * @property integer $exchange_rate
 * @property string $currency_code
 * @property string|null $description
 * @property RevenueCategory|string $category
 * @property string $billing_method
 * @property string|null $reference
 * @property DateTimeInterface|null $refunded_at
 *
 * @property-read Account|null $account
 * @property-read User|null $customer
 */
class Revenue extends Model
{
    use CanBePaid, CastsEnums, SoftDeletes, HasDiscounts, ResolvesBillingMethod;

    protected $fillable = [
        'account_id',
        'customer_email',
        'amount',
        'earned_points',
        'exchange_rate',
        'currency_code',
        'description',
        'category',
        'billing_method',
        'reference',
        'refunded_at',
        'paid_at'
    ];

    protected $dispatchesEvents = [
        'refunding' => RevenueRefunding::class,
        'refunded' => RevenueRefunded::class
    ];

    public $enumCasts = [
        'category' => RevenueCategory::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Revenue $revenue) {
            if (blank($revenue->earned_points) && $revenue->eligibleForEarningPoints()) {
                $revenue->earned_points = points_for($revenue->amount);
            }
        });
    }

    /**
     * Whether this revenue is eligible for earning points
     *
     * @return bool
     */
    public function eligibleForEarningPoints(): bool
    {
        return $this->account
            && $this->category->is(RevenueCategory::PURCHASES);
    }

    /**
     * Optionally, the agent account that generated the Revenue.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The customer that generated the revenue
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
     * Refund the given or full amount to the customer.
     *
     * @param integer|null $amount
     * @param string|null $reason
     * @throws Throwable
     * @return $this
     */
    public function refund(int $amount = null, string $reason = null)
    {
        $this->fireModelEvent('refunding');

        $this->billingMethod()->refund(
            $amount ?? $this->amount,
            $this,
            static function (BillingMethodOptions $options) use ($reason) {
                if ($reason) {
                    $options->setDescription($reason);
                }
            }
        );

        $this->update(['refunded_at' => $this->freshTimestampString()]);

        $this->fireModelEvent('refunded');

        return $this;
    }

    /**
     * Withdraw the amount from the associated account.
     *
     * @return void
     */
    public function withdrawFromAccount(): void
    {
        event(
            new MoneyWithdrawnFromBalance(
                $this->account->uuid,
                $this->amount,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Add the amount of earned points to the associated account.
     *
     * @return void
     */
    public function addEarnedPointsToAccount(): void
    {
        event(
            new PointsEarned(
                $this->account->uuid,
                $this->earned_points,
                static::class,
                $this->getKey()
            )
        );
    }

    /**
     * Add the amount to the account ledger balance(s).
     *
     * @return void
     */
    public function depositToAccount(): void
    {
        event(
            new MoneyDepositedToBalance(
                $this->account->uuid,
                $this->amount,
                static::class,
                $this->getKey()
            )
        );
    }
}
