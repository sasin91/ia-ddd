<?php


namespace App\Domains\Billing\Methods;

use App\Domains\Billing\Methods\Concerns\ResolvesAccount;
use App\Domains\Billing\Methods\Concerns\ValidatesAccount;
use App\Domains\Agent\Models\AccountLedger;
use App\Domains\Billing\Enums\ExpenseCategory;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\Exceptions\WithdrawFailed;
use App\Domains\Billing\Methods\Concerns\AppliesDiscounts;
use App\Domains\Billing\Models\Expense;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Contracts\BillingMethod;
use Illuminate\Database\Eloquent\Model;
use function class_basename;
use function optional;
use function tap;
use function throw_unless;
use Throwable;

class Balance implements BillingMethod
{
    use ResolvesAccount,
        ValidatesAccount,
        AppliesDiscounts;

    /**
     * @inheritDoc
     */
    public function withdraw(int $amount, $accountOrEmail, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $options = BillingMethodOptions::parse($options);

        if ($options->hasDiscounts()) {
            $amount = $this->applyDiscounts(
                $options->getDiscounts(),
                $amount
            );
        }

        $this->validateSufficientBalance(
            $amount,
            $account->ledgers()->where('currency', $options->getCurrencyCode())->first()
        );

        return tap(new Revenue([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $amount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => RevenueCategory::PURCHASES,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription(),
        ]), function (Revenue $revenue) use ($options, $account) {
            $revenue->account()->associate($account);

            $revenue->discounts()->saveMany(
                $options->getDiscounts()
            );

            $revenue->saveOrFail();

            $revenue->withdrawFromAccount();
            $revenue->addEarnedPointsToAccount();
        });
    }

    /**
     * @inheritDoc
     */
    public function deposit(int $amount, $accountOrEmail, $options = null)
    {
        $account = $this->resolveAccount($accountOrEmail);

        $this->validateAccount($account);

        $options = BillingMethodOptions::parse($options);

        return tap(new Revenue([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $amount,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'billing_method' => class_basename($this),
            'category' => RevenueCategory::DEPOSITS,
            'reference' => $options->getReference(),
            'paid_at' => $options->getPaidAt(),
            'description' => $options->getDescription(),
        ]), function (Revenue $revenue) use ($options, $amount, $account) {
            $revenue->account()->associate($account);

            $revenue->saveOrFail();

            $revenue->depositToAccount();
        });
    }

    /**
     * @inheritDoc
     */
    public function refund($revenueOrAmount, $accountOrEmail, $options = null)
    {
        $this->validateAccount(
            $account = $this->resolveAccount($accountOrEmail)
        );

        $options = BillingMethodOptions::parse($options);

        return tap(new Expense([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $$revenueOrAmount,
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

            if ($account) {
                $expense->account()->associate($account);
            }

            $expense->saveOrFail();

            if ($expense->category->is(ExpenseCategory::REFUND) && $expense->account) {
                $expense->refundToAccount();

                if ($expense->revenue) {
                    $expense->withdrawEarnedPointsFromAccount();
                }
            }
        });
    }

    /**
     * @param int $amount
     * @param AccountLedger|Model $ledger
     * @throws Throwable
     */
    protected function validateSufficientBalance(int $amount, AccountLedger $ledger = null): void
    {
        throw_unless(
            optional($ledger)->balance >= $amount,
            WithdrawFailed::insufficientBalance($amount, $ledger)
        );
    }
}
