<?php

namespace App\Domains\Agent\Models;

use App\Domains\Agent\Enums\AccountType;
use App\Domains\Agent\Events\CreateAccount;
use App\User;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use function config;
use function data_get;

/**
 * Class Account
 * @package App
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
        return $query->where('uuid', $uuid)->firstOrFail();
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
}
