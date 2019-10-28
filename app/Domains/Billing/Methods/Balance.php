<?php


namespace App\Domains\Billing\Methods;

use App\Domains\Billing\Methods\Concerns\ResolvesAccount;
use App\Domains\Billing\Methods\Concerns\ValidatesAccount;
use App\Domains\Agent\Models\AccountLedger;
use App\Domains\Billing\Enums\PaymentCategory;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\Exceptions\WithdrawFailed;
use App\Domains\Billing\Methods\Concerns\AppliesDiscounts;
use App\Domains\Billing\Models\Payment;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Contracts\BillingMethod;
use function class_basename;
use function optional;
use function tap;
use function throw_unless;
use Throwable;

class  Balance implements BillingMethod
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
            $account->ledgers->firstWhere('currency', $options->getCurrencyCode())
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

        return tap(new Payment([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $$revenueOrAmount,
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

            if ($account) {
                $payment->account()->associate($account);
            }

            $payment->saveOrFail();

            if ($payment->category->is(PaymentCategory::REFUND) && $payment->account) {
                $payment->refundToAccount();

                if ($payment->revenue) {
                    $payment->withdrawEarnedPointsFromAccount();
                }
            }
        });
    }

    /**
     * @param int $amount
     * @param AccountLedger $ledger
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
