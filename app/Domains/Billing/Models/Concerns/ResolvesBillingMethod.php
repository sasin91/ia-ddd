<?php

namespace App\Domains\Billing\Models\Concerns;

use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Contracts\BillingMethod as BillingMethodContract;
use App\Domains\Billing\Models\Revenue;

trait ResolvesBillingMethod
{

    /**
     * The billing method in use
     *
     * @return BillingMethodContract|null
     */
    public function billingMethod(): ?BillingMethodContract
    {
        return BillingMethod::make($this->billing_method);
    }
}
