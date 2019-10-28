<?php


namespace App\Domains\Booking\Models\Concerns;

use App\Domains\Booking\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait Serviceable
 *
 * @property Collection $services
 */
trait Serviceable
{
    /**
     * The services attached to the model
     *
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
