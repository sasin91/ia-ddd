<?php

namespace App\Domains\Billing\Methods\Concerns;

use App\Domains\Agent\Enums\AccountType;
use App\Domains\Agent\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function is_email;
use function with;

trait ResolvesAccount
{
    /**
     * @param $account
     * @return string|Account|null
     * @throws ModelNotFoundException
     */
    protected function resolveAccount($account):?Account
    {
        if ($account instanceof Account) {
            return $account->loadMissing('ledgers');
        }

        return with(Account::with('ledgers'), function ($query) use ($account) {
            /** @var Builder $query */
            if (is_email($account)) {
                return $query
                    ->whereHas('owner', function ($query) use ($account) {
                        $query->where('email', $account);
                    })
                    ->where('type', AccountType::MAIN)
                    ->latest()
                    ->first();
            }

            return $query
                ->where('uuid', $account)
                ->first();
        });
    }
}
