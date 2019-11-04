<?php

namespace App\Domains\Booking\Models;

use App\User;
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
    use SoftDeletes;

    protected $fillable = [
        'creator_id',
        'age_group_id',
        'title',
        'first_name',
        'last_name',
        'gender',
        'phone',
        'birthdate',
        'nationality',
        'citizenship',
        'passport',
        'visa',
        'visa_country',
        'passport_issued_at',
        'passport_expires_at',
        'visa_expires_at',
    ];

    protected $casts = [
        'passport_expires_at' => 'datetime',
        'passport_issued_at' => 'datetime',
        'visa_expires_at' => 'datetime',
        'birthdate' => 'date',
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
        return $this->belongsTo(AgeGroup::class);
    }

    /**
     * The ticket the passenger has made
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Allows setting the full_name attribute.
     *
     * @example "John Jeremia Doe" => (first_name = John, middle_name = Jeremia, last_name = Doe)
     * @param string $value
     */
    public function setFullNameAttribute($value)
    {
        $fragments = explode(' ', $value);

        $this->setAttribute('first_name', $fragments[0]);

        if (count($fragments) === 2) {
            $this->setAttribute('last_name', $fragments[1]);
        } else {
            $this->setAttribute('middle_name', $fragments[1]);
            $this->setAttribute('last_name', $fragments[2]);
        }
    }

    /**
     * Composes the full name and allows calling full_name
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->title} {$this->first_name} {$this->middle_name} {$this->last_name}";
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
