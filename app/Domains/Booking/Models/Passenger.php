<?php

namespace App\Domains\Booking\Models;

use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Domains\Booking\Enums\PassengerGender;
use App\Domains\Booking\Enums\PassengerTitle;
use App\User;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use function count;
use function encrypt;
use function explode;

class Passenger extends Model
{
    use CastsEnums, SoftDeletes;

    protected $fillable = [
        'creator_id',
        'age_group',
        'title',
        'name',
        'gender',
        'phone',
        'birthdate',
        'nationality',
        'citizenship',
        'passport',
        'visa',
        'visa_country',
        'passport_expires_at',
        'visa_expires_at',
    ];

    protected $casts = [
        'passport_expires_at' => 'datetime',
        'visa_expires_at' => 'datetime',
        'birthdate' => 'date',
    ];

    public $enumCasts = [
        'title' => PassengerTitle::class,
        'gender' => PassengerGender::class,
        'nationality' => Nationality::class,
        'citizenship' => Citizenship::class
    ];

    /**
     * Whom the passenger is created by
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * The age group this passenger belongs to
     *
     * @return BelongsTo
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class, 'age_group', 'name');
    }

    /**
     * The ticket the passenger has made
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Composes the full name and allows calling full_name
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->title} {$this->name}";
    }

    /**
     * Encrypts given passport
     *
     * @param $value
     * @return Passenger
     */
    public function setPassportAttribute($value): Passenger
    {
        $this->attributes['passport'] = encrypt($value);

        return $this;
    }

    /**
     * Decrypts the encrypted passport
     *
     * @param string $value
     * @return string|null
     */
    public function getPassportAttribute($value): ?string
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            report($e);

            return $value;
        }
    }

    /**
     * Encrypts given visa number
     *
     * @param $value
     * @return Passenger
     */
    public function setVisaAttribute($value): Passenger
    {
        $this->attributes['visa'] = encrypt($value);

        return $this;
    }

    /**
     * Decrypts the encrypted visa number
     *
     * @param string $value
     * @return string|null
     */
    public function getVisaAttribute($value): ?string
    {
        try {
            return decrypt($value);
        } catch (DecryptException $decryptException) {
            report($decryptException);

            return $value;
        }
    }
}
