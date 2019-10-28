<?php


namespace App\Domains\Billing\Events;


use App\Domains\Billing\Models\Revenue;

class RevenueRefunded
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
