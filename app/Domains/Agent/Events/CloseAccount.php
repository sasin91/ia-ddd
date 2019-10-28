<?php


namespace App\Domains\Agent\Events;


use App\Domains\Billing\Methods\Bank;
use App\Domains\Agent\Models\Account;
use App\Domains\Billing\Contracts\BillingMethod;
use Spatie\EventSourcing\ShouldBeStored;
use function config;
use function get_class;
use function is_object;

class CloseAccount implements ShouldBeStored
{
    /**
     * UUID of the Account.
     *
     * @var string
     */
    public $accountUuid;

    /**
     * Recipient email
     *
     * @var string
     */
    public $recipientEmail;

    /**
     * The desired method for paying out remaining balance(s).
     *
     * @var string
     */
    public $billingMethod = Bank::class;

    /**
     * The desired payout currency
     *
     * @var string
     */
    public $payoutCurrency;

    /**
     * The amount of remaining balance
     *
     * @var int
     */
    public $amountInDispute = 0;

    /**
     * CloseAccount constructor.
     *
     * @param string $accountUuid
     * @param string|BillingMethod $billingMethod
     * @param string|null $payoutCurrency
     */
    public function __construct(string $accountUuid, $billingMethod, string $payoutCurrency = null)
    {
        $this->payoutCurrency = $payoutCurrency ?? config('currency.default');

        $account = Account::with('ledgers', 'owner')->findByUUID($accountUuid);

        $this->recipientEmail = $account->owner->email;
        $this->accountUuid = $accountUuid;

        $this->billingMethod = is_object($billingMethod) ? get_class($billingMethod) : $billingMethod;
        $this->amountInDispute = $account->ledgers->where('currency', $this->payoutCurrency)->sum('balance');
    }
}
