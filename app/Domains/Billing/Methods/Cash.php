<?php


namespace App\Domains\Billing\Methods;

use App\Domains\Billing\Methods\Concerns\ResolvesAccount;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Contracts\BillingMethod;
use App\Domains\Billing\Enums\ExpenseCategory;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\Models\Expense;
use App\Domains\Billing\Models\Revenue;
use function class_basename;
use function is_email;
use function is_null;
use function is_object;
use function tap;

class Cash implements BillingMethod
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
            'category' => RevenueCategory::CASH,
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
    public function refund($revenueOrAmount, $accountOrEmail, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $options = BillingMethodOptions::parse($options);

        return tap(new Expense([
            'amount' => is_object($revenueOrAmount) ? $revenueOrAmount->amount : $revenueOrAmount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => ExpenseCategory::REFUND,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription(),
        ]), function (Expense $expense) use ($options, $revenueOrAmount, $account) {
            if ($revenueOrAmount instanceof Revenue) {
                $expense->revenue()->associate($revenueOrAmount);
            }

            if ($account instanceof Account) {
                $expense->account()->associate($account);
            }

            if ($options->getCustomerEmail()) {
                $expense->customer_email = $options->getCustomerEmail();
            } elseif ($account && $account->owner) {
                $expense->customer()->associate($account->owner);
            }

            $expense->saveOrFail();
        });
    }
}
