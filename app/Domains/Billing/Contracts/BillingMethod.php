<?php

namespace App\Domains\Billing\Contracts;

use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Models\Expense;
use Closure;
use App\Domains\Agent\Models\Account;
use Throwable;
use App\Domains\Billing\Models\Revenue;

interface BillingMethod
{
    /**
     * Charge the customer then record it in their ledger.
     *
     * @param int $amount
     * @param Account|string $accountOrEmail
     * @param array|Closure|BillingMethodOptions|null $options
     * @return Revenue
     * @throws Throwable
     */
    public function withdraw(int $amount, $accountOrEmail, $options = null);

    /**
     * Charge the customer then add the amount to their ledger
     *
     * @param int $amount
     * @param Account|string $accountOrEmail
     * @param array|Closure|BillingMethodOptions|null $options
     * @return Revenue
     * @throws Throwable
     */
    public function deposit(int $amount, $accountOrEmail, $options = null);

    /**
     * Refund a full Revenue or partial amount to the customer then record it in their ledger.
     *
     * @param Revenue|integer $revenueOrAmount
     * @param Account|string $accountOrEmail
     * @param array|Closure|BillingMethodOptions|null $options
     * @return Expense
     * @throws Throwable
     */
    public function refund($revenueOrAmount, $accountOrEmail, $options = null);
}
