<?php

namespace App\Domains\Agent\Events;

use Spatie\EventSourcing\ShouldBeStored;

class PointsSpent implements ShouldBeStored
{
    /**
     * UUID of the Account
     *
     * @var string
     */
    public $accountUuid;

    /**
     * Amount of points to subtract from the Account
     *
     * @var int
     */
    public $amount;

    /**
     * The class name of the model that caused this event.
     *
     * @options [App\Expense]
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
     * PointsSpent constructor.
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
