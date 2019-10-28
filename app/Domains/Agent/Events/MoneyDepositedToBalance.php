<?php

namespace App\Domains\Agent\Events;

use Spatie\EventSourcing\ShouldBeStored;

class MoneyDepositedToBalance implements ShouldBeStored
{
    /**
     * UUID of the Account
     *
     * @var string
     */
    public $accountUuid;

    /**
     * Amount of money to add to the Account Balance
     *
     * @var int
     */
    public $amount;

    /**
     * The class name of the model that caused this event.
     *
     * @options [App\Revenue,App\Payment]
     * @var null|string
     */
    public $causerType;

    /**
     * Primary key of the model that caused this event.
     *
     * @var null|integer
     */
    public $causerId;

    /**
     * MoneyDepositedToBalance constructor.
     *
     * @param string $accountUuid
     * @param int $amount
     * @param string|null $causerType
     * @param string|null $causerId
     */
    public function __construct(string $accountUuid, int $amount, string $causerType, string $causerId)
    {
        $this->accountUuid = $accountUuid;
        $this->amount = $amount;
        $this->causerType = $causerType;
        $this->causerId = $causerId;
    }
}
