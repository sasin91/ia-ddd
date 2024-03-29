<?php

namespace App;

use App\Domains\Agent\Models\Account;
use App\Domains\Agent\Models\Agency;
use App\Domains\Billing\Models\Expense;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Booking\Models\Passenger;
use App\Domains\Booking\Models\Ticket;
use App\Domains\Booking\Models\Trip;
use App\Domains\Booking\Models\TicketChange;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App
 *
 * @method static Builder|User withLastLogin()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Impersonate, Notifiable, SoftDeletes, HasPermissions, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
        'photo_url', 'country_code', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Eager Load the last login
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeWithLastLogin($query): void
    {
        $query->addSubSelect(
            Login::query()
            ->select('id')
            ->whereColumn('user_id', 'users.id')
            ->latest()
            ->limit(1),
            'last_login_id'
        )->with('lastLogin');
    }

    /**
     * The tickets bought by this user
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'buyer_id');
    }

    /**
     * The users latest tickets
     *
     * @return HasMany
     */
    public function latestTickets(): HasMany
    {
        return $this->tickets()->latest()->take(15);
    }

    /**
     * The users latest changes
     *
     * @return HasMany
     */
    public function latestRequestedChanges(): HasMany
    {
        return $this->requestedChanges()->latest()->take(15);
    }

    /**
     * The ticket changes requested by this user
     *
     * @return HasMany
     */
    public function requestedChanges(): HasMany
    {
        return $this->hasMany(TicketChange::class, 'requested_by');
    }

    /**
     * The ticket changes that's handled by this user
     *
     * @return HasMany
     */
    public function handledChanges(): HasMany
    {
        return $this->hasMany(TicketChange::class, 'handled_by');
    }

    /**
     * The passengers this user has created
     *
     * @return HasMany
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class, 'creator_id');
    }

    /**
     * Agent agencies
     *
     * @return HasMany
     */
    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class, 'owner_id');
    }

    /**
     * Agent accounts
     *
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'owner_id');
    }

    /**
     * The expenses made on accounts by this user
     *
     * @return HasManyThrough
     */
    public function expenses(): HasManyThrough
    {
        return $this->hasManyThrough(Expense::class, Account::class, 'owner_id');
    }

    /**
     * The revenues made on accounts by this user
     *
     * @return HasManyThrough
     */
    public function revenues(): HasManyThrough
    {
        return $this->hasManyThrough(Revenue::class, Account::class, 'owner_id');
    }

    /**
     * Dynamic relationship for avoiding loading all logins when only latest is relevant.
     *
     * @return BelongsTo
     */
    public function lastLogin(): BelongsTo
    {
        return $this->belongsTo(Login::class);
    }

    /**
     * The users login records
     *
     * @return HasMany
     */
    public function logins(): HasMany
    {
        return $this->hasMany(Login::class);
    }

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return  bool
     */
    public function canImpersonate()
    {
        return $this->hasPermissionTo('Impersonate other users');
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return  bool
     */
    public function canBeImpersonated()
    {
        return $this->roles->isEmpty()
            || $this->hasRole('Agent');
    }
}
