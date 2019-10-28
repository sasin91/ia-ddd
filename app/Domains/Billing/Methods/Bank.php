<?php

namespace App\Domains\Billing\Methods;

use App\Domains\Billing\Methods\Concerns\ResolvesAccount;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Contracts\BillingMethod;
use App\Domains\Billing\Enums\PaymentCategory;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\Models\Payment;
use App\Domains\Billing\Models\Revenue;
use function class_basename;
use function is_email;
use function is_null;
use function is_object;
use function tap;

class Bank implements BillingMethod
{
    use ResolvesAccount;

    /**
     * @inheritDoc
     */
    public function withdraw(int $amount, $accountOrEmail, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $options = BillingMethodOptions::parse($options);

        if (is_email($accountOrEmail) && is_null($options->getCustomerEmail())) {
            $options->setCustomerEmail($accountOrEmail);
        }

        return tap(new Revenue([
            'amount' => $amount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => RevenueCategory::BANK_TRANSFERS,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription(),
        ]), function (Revenue $revenue) use ($options, $account) {
            if ($account) {
                $revenue->account()->associate($account);
            }

            if ($options->getCustomerEmail()) {
                $revenue->customer_email = $options->getCustomerEmail();
            } elseif ($account && $account->owner) {
                $revenue->customer_email = $account->owner->email;
            }

            $revenue->saveOrFail();
        });
    }

    /**
     * @inheritDoc
     */
    public function deposit(int $amount, $accountOrEmail, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $options = BillingMethodOptions::parse($options);

        return tap(new Revenue([
            'amount' => $amount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'billing_method' => class_basename($this),
            'category' => RevenueCategory::DEPOSITS,
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription()
        ]), function (Revenue $revenue) use ($options, $amount, $account) {
            if ($account) {
                $revenue->account()->associate($account);
            }

            if ($options->getCustomerEmail()) {
                $revenue->customer_email = $options->getCustomerEmail();
            } elseif ($account && $account->owner) {
                $revenue->customer()->associate($account->owner);
            }

            $revenue->saveOrFail();

            if ($revenue->isPaid()) {
                $revenue->depositToAccount();
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function refund($accountOrEmail, $revenueOrAmount, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $options = BillingMethodOptions::parse($options);

        return tap(new Payment([
            'amount' => is_object($revenueOrAmount) ? $revenueOrAmount->amount : $revenueOrAmount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => PaymentCategory::REFUND,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription(),
        ]), function (Payment $payment) use ($options, $revenueOrAmount, $account) {
            if ($revenueOrAmount instanceof Revenue) {
                $payment->revenue()->associate($revenueOrAmount);
            }

            if ($account instanceof Account) {
                $payment->account()->associate($account);
            }

            if ($options->getCustomerEmail()) {
                $payment->customer_email = $options->getCustomerEmail();
            } elseif ($account && $account->owner) {
                $payment->customer()->associate($account->owner);
            }

            $payment->saveOrFail();
        });
    }
}
