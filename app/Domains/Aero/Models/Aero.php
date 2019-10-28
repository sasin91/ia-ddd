<?php

namespace App\Domains\Aero\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aero extends Model
{
    protected $fillable = [
        'name',
        'terminal_ip',
        'terminal_emulator'
    ];

    /**
     * Terminal commands executed or pending
     *
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(AeroAction::class);
    }
}
