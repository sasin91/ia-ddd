<?php

namespace App\Domains\Billing\Exceptions;

use App\Domains\Agent\Models\Account;
use App\Domains\Agent\Models\AccountLedger;
use function data_get;
use function get_class;
use function is_object;
use function sprintf;
use RuntimeException;

class WithdrawFailed extends RuntimeException
{
    public static function invalidToken(string $token)
    {
        return new static("Invalid payment token given [{$token}].", 422);
    }

    public static function invalidCustomer($customer)
    {
        if (is_object($customer)) {
            $class = get_class($customer);
            return new static("Expected customer to be a registered User. instance of [{$class}] given.");
        }

        return new static("Expected customer to be a registered User.");
    }

    public static function insufficientBalance(int $amount, ?AccountLedger $ledger = null)
    {
        return new static(
            sprintf(
                "%i is not sufficient to withdraw %i",
                data_get($ledger, 'balance', 0),
                $amount
            )
        );
    }

    public static function insufficientPoints(int $amount, Account $account)
    {
        return new static(
            "{$account->points} is not sufficient to withdrawn {$amount}"
        );
    }
}
