<?php


namespace App\Domains\Billing\Models\Concerns;


use App\Domains\Billing\Models\Discount;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDiscounts
{
    /**
     * The attached discounts
     *
     * @return MorphMany
     */
    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discounted');
    }

    /**
     * The active discounts
     *
     * @return MorphMany
     */
    public function activeDiscounts(): MorphMany
    {
        return $this->discounts()->where('active', true);
    }
}
