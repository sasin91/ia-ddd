<?php


namespace App\Domains\Billing\Methods;

use App\Domains\Billing\Methods\Concerns\ResolvesAccount;
use App\Domains\Billing\Methods\Concerns\ValidatesAccount;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Configuration\BillingMethodOptions;
use App\Domains\Billing\Contracts\BillingMethod;
use App\Domains\Billing\Enums\PaymentCategory;
use App\Domains\Billing\Exceptions\WithdrawFailed;
use App\Domains\Billing\Models\Payment;
use App\Domains\Billing\Models\Revenue;
use function class_basename;
use function is_object;
use function points_for;
use function tap;
use function throw_unless;
use Throwable;

/**
 * Class Points
 * @package App\Domains\Billing\Methods
 *
 * Accepts the raw monetary cost & automatically calculates the amount of points
 * This way we can keep track of both values without compromising too much.
 */
class Points implements BillingMethod
{
    use ResolvesAccount,ValidatesAccount;

    /**
     * @inheritDoc
     */
    public function withdraw(int $amount, $accountOrEmail, $options = null)
    {
        $this->validateAccount(
            $account = $this->resolveAccount($accountOrEmail)
        );

        $options = BillingMethodOptions::parse($options);

        $this->validateSufficientPoints(
            $points = points_for($amount, $options->getCurrencyCode()),
            $account
        );

        return tap(new Payment([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $amount,
            'points' => $points,
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => PaymentCategory::POINTS_PURCHASE,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'description' => $options->getDescription()
        ]), function (Payment $payment) use ($options, $account) {
            $payment->account()->associate($account);
            $payment->customer()->associate($account->owner);

            $payment->saveOrFail();

            $payment->withdrawPointsFromAccount();
        });
    }

    /**
     * @inheritDoc
     */
    public function deposit(int $amount, $accountOrEmail, $options = null)
    {
        $this->validateAccount(
            $account = $this->resolveAccount($accountOrEmail)
        );

        $options = BillingMethodOptions::parse($options);

        return tap(new Payment([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $amount,
            'points' => points_for($amount, $options->getCurrencyCode()),
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => PaymentCategory::POINTS_DEPOSIT,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'description' => $options->getDescription()
        ]), function (Payment $payment) use ($options, $account) {
            $payment->account()->associate($account);
            $payment->customer()->associate($account->owner);

            $payment->saveOrFail();

            $payment->depositPointsToAccount();
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

        $amount = is_object($revenueOrAmount) ? $revenueOrAmount->amount : $revenueOrAmount;

        $options = BillingMethodOptions::parse($options);

        return tap(new Payment([
            'customer_email' => $options->getCustomerEmail() ?? $account->owner->email,
            'amount' => $amount,
            'points' => points_for($amount, $options->getCurrencyCode()),
            'currency_code' => $options->getCurrencyCode(),
            'exchange_rate' => $options->getExchangeRate(),
            'category' => PaymentCategory::POINTS_REFUND,
            'billing_method' => class_basename($this),
            'reference' => $options->getReference(),
            'description' => $options->getDescription()
        ]), function (Payment $payment) use ($options, $account, $revenueOrAmount) {
            if ($revenueOrAmount instanceof Revenue) {
                $payment->revenue()->associate($revenueOrAmount);
            }

            $payment->account()->associate($account);
            $payment->customer()->associate($account->owner);

            $payment->saveOrFail();

            $payment->refundPointsToAccount();
        });
    }

    /**
     * @param int $amount
     * @param Account $account
     * @throws Throwable
     */
    protected function validateSufficientPoints(int $amount, Account $account): void
    {
        throw_unless(
            $account->points >= $amount,
            WithdrawFailed::insufficientPoints($amount, $account)
        );
    }
}
