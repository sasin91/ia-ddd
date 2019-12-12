<?php

namespace App\Domains\Agent\Models;

use Throwable;
use App\User;
use function config;
use Ramsey\Uuid\Uuid;
use function data_get;
use BenSampo\Enum\Traits\CastsEnums;
use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Models\Revenue;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Agent\Enums\AccountType;
use Illuminate\Database\Eloquent\Builder;
use App\Domains\Agent\Events\CreateAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class Account
 * @package App
 * @mixin Builder
 *
 * @property string $uuid
 * @property integer $owner_id
 * @property integer|null $agency_id
 * @property AccountType|string $type
 * @property string|null $description
 * @property integer $points
 *
 * @method static Account findByEvent($event)
 * @method static Account findByUUID(string $uuid)
 * @method static Account withBalance(?string $currency)
 * @property Collection $ledgers
 * @property User $owner
 * @property Agency|null $agency
 */
class Account extends Model
{
    use CastsEnums, SoftDeletes;

    protected $fillable = [
        'uuid',
        'agency_id',
        'owner_id',
        'type',
        'description',
        'points'
    ];

    protected $casts = [
        'points' => 'integer'
    ];

    public $enumCasts = [
        'type' => AccountType::class
    ];

    /**
     * Create the account through the AccountProjector
     *
     * @see AccountProjector#createAccount
     * @param array $attributes
     * @return string
     * @throws \Exception
     */
    public static function createThroughEventProjector(array $attributes): string
    {
        if (!isset($attributes['uuid'])) {
            $attributes['uuid'] = (string)Uuid::uuid4();
        }

        /**
         * Fire an event that'll cause the AccountProjector to create the actual Account while keeping a record of the event,
         * So it'll get recreated exactly as it was if replayed.
         */
        event(new CreateAccount($attributes));

        return $attributes['uuid'];
    }

    /**
     * Find the account by a stored event
     *
     * @param Builder $query
     * @param object $event
     * @throws ModelNotFoundException
     * @return Account|Model
     */
    public function scopeFindByEvent($query, $event)
    {
        return $this->scopeFindByUUID(
            $query,
            data_get($event, 'accountUuid')
        );
    }

    /**
     * Find the account by the UUID
     *
     * @param Builder $query
     * @param string $uuid
     * @return Account|Model
     */
    public function scopeFindByUUID($query, string $uuid)
    {
        return $query->where('uuid', $uuid)->first();
    }

    /**
     * Include the balance amount from the Ledger on the Account
     *
     * @param Builder $query
     * @param string $currency
     * @return void
     */
    public function scopeWithBalance($query, string $currency): void
    {
        $query->addSubSelect(
            AccountLedger::query()
            ->select('balance')
            ->whereColumn('account_id', 'accounts.id')
            ->where('currency', $currency)
            ->limit(1),
            'balance'
        );
    }

    /**
     * The agency the account belongs to.
     * Technically the owner should also be part of the agency.
     *
     * @return BelongsTo
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * The User that owns the Account
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The stored events involving this Account
     *
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(
            config('event-sourcing.stored_event_model'),
            'event_properties->accountUuid',
            'uuid'
        );
    }

    /**
     * A tab for each supported currency
     *
     * @return HasMany
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(
            AccountLedger::class,
            'account_id',
            'id'
        );
    }

    /**
     * The applicable commissions
     *
     * @return HasMany
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Withdraw a given amount from the account
     *
     * @param integer $amount
     * @param string $method
     * @param string $currency
     * @throws Throwable
     * @return Revenue
     */
    public function withdraw(int $amount, $method = null, ?string $currency = null)
    {
        return BillingMethod::make($method ?? 'balance')->withdraw(
            $amount,
            $this,
            ['currency' => $currency]
        );
    }

    /**
     * Deposit given amount to the account
     *
     * @param integer $amount
     * @param string $method
     * @param string $currency
     * @throws Throwable
     * @return Revenue
     */
    public function deposit(int $amount, $method = null, ?string $currency = null)
    {
        return BillingMethod::make($method ?? 'balance')->deposit(
            $amount,
            $this,
            ['currency' => $currency]
        );
    }
}
