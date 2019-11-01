<?php

namespace App\Domains\Billing\Models\Concerns;

use App\Domains\Billing\Models\Discount;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Discountable
{
    /**
     * The attached discounts
     *
     * @return MorphMany
     */
    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discountable');
    }
}