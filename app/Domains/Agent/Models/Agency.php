<?php

namespace App\Domains\Agent\Models;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Agency
 * @package App\Domains\Agent\Models
 *
 * The Agent office
 *
 * @property integer $owner_id
 * @property string $company
 * @property string $name
 * @property string $phone
 * @property string $location
 * @property string $country
 *
 * @property-read User $owner
 * @property-read Collection<Account> $accounts
 */
class Agency extends Model
{
    protected $fillable = [
        'owner_id',
        'company',
        'name',
        'phone',
        'location',
        'country'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
