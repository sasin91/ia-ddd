<?php


namespace App\Domains\Billing\Methods\Concerns;


use App\Domains\Billing\Models\Discount;
use function abs;
use function collect;

trait AppliesDiscounts
{
    /**
     * Apply the added discounts
     *
     * @param array $discounts
     * @param int $amount
     * @return int
     */
    public function applyDiscounts($discounts, int $amount): int
    {
        return collect($discounts)
            ->where('active', true)
            ->reduce(function (int $result, Discount $discount) {
                return $result - abs($discount->amount);
            }, $amount);
    }
}
