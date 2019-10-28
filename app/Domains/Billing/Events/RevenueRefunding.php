<?php


namespace App\Domains\Billing\Events;


use App\Domains\Billing\Models\Revenue;

class RevenueRefunding
{
    /**
     * @var Revenue
     */
    public $revenue;

    public function __construct(Revenue $revenue)
    {
        $this->revenue = $revenue;
    }
}
