<?php

namespace App\Domains\Booking\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder covering($age)
 */
class AgeGroup extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'icon',
        'from','to',
        'passport_required',
        'luggage_limit'
    ];

    protected $casts = [
        'passport_required'     =>  'boolean',
        'from'                  =>  'integer',
        'to'                    =>  'integer'
    ];

    /**
     * Get the age group(s) covering a given age
     *
     * @param   Builder $query
     * @param   int  $age
     *
     * @return  Builder
     */
    public function scopeCovering($query, int $age)
    {
        return $query->where('to', '>=', $age)
            ->where('from', '<=', $age);
    }

    public function getNameAttribute($name)
    {
        return __(ucfirst($name));
    }
}
