<?php

namespace App\Domains\Agent\Projectors;

use App\Domains\Agent\Events\CloseAccount;
use App\Domains\Agent\Events\CreateAccount;
use App\Domains\Agent\Events\MoneyDepositedToBalance;
use App\Domains\Agent\Events\MoneyRefundedToBalance;
use App\Domains\Agent\Events\MoneyWithdrawnFromBalance;
use App\Domains\Agent\Events\PointsDepositedToAccount;
use App\Domains\Agent\Events\PointsEarned;
use App\Domains\Agent\Events\PointsSpent;
use App\Domains\Agent\Events\PointsWithdrawnFromAccount;
use App\Domains\Agent\Models\Account;
use App\Domains\Agent\Models\AccountLedger;
use App\Domains\Billing\BillingMethod;
use Spatie\EventSourcing\Projectors\ProjectsEvents;
use Spatie\EventSourcing\Projectors\QueuedProjector;
use Spatie\EventSourcing\StoredEvent;

use function config;
use function get_class;

class AccountProjector implements QueuedProjector
{
    use ProjectsEvents;

    /*
     * Here you can specify which event should trigger which method.
     */
    protected $handlesEvents = [
        CreateAccount::class,
        CloseAccount::class,
        MoneyDepositedToBalance::class,
        MoneyRefundedToBalance::class,
        MoneyWithdrawnFromBalance::class,
        PointsEarned::class,
        PointsSpent::class,
        PointsWithdrawnFromAccount::class,
        PointsDepositedToAccount::class
    ];

    public function resetState()
    {
        AccountLedger::query()->truncate();
        Account::query()->truncate();
    }

    public function onCreateAccount(CreateAccount $event)
    {
        /** @var Account $account */
        $account = Account::query()->create($event->attributes);

        foreach (config('currency.supported') as $currency) {
            /** @var AccountLedger $ledger */
            $ledger = $account->ledgers()->create([
                'currency' => $currency,
                'balance' => 0
            ]);

            if (isset($event->attributes['balance'])) {
                $ledger->update([
                    'balance' => $event->attributes['balance'] * $ledger->exchangeRate()->value
                ]);
            }
        }
    }

    public function onCloseAccount(CloseAccount $event)
    {
        $account = Account::findByEvent($event);

        BillingMethod::make($event->billingMethod)->refund(
            $event->amountInDispute,
            $event->recipientEmail,
            [
                'description' => 'Account closed.'
            ]
        );

        $account->ledgers()->delete();

        $account->delete();
    }

    public function onMoneyDepositedToBalance(MoneyDepositedToBalance $event, StoredEvent $storedEvent)
    {
        $this->addMoneyToAccountBalance($event, $storedEvent);
    }

    public function onMoneyRefundedToBalance(MoneyRefundedToBalance $event, StoredEvent $storedEvent)
    {
        $this->addMoneyToAccountBalance($event, $storedEvent);
    }

    public function onMoneyWithdrawnFromBalance(MoneyWithdrawnFromBalance $event, StoredEvent $storedEvent)
    {
        $this->subtractMoneyFromAccountBalance($event, $storedEvent);
    }

    public function onPointsEarned(PointsEarned $event)
    {
        $this->addPointsToAccount($event);
    }

    public function onPointsSpent(PointsSpent $event)
    {
        $this->subtractPointsFromAccount($event);
    }

    public function onPointsWithdrawnFromAccount(PointsWithdrawnFromAccount $event)
    {
        $this->subtractPointsFromAccount($event);
    }

    public function onPointsDepositedToAccount(PointsDepositedToAccount $event)
    {
        $this->addPointsToAccount($event);
    }

    private function addMoneyToAccountBalance($event, $storedEvent)
    {
        $account = Account::findByEvent($event);

        $account->ledgers->each(function (AccountLedger $ledger) use ($event, $storedEvent) {
            $exchangeRate = $ledger->exchangeRate()->value;

            $ledger->movements()->create([
                'stored_event_id' => $storedEvent->id,
                'stored_event_type' => get_class($event),
                'causer_type' => $event->causerType,
                'causer_id' => $event->causerId,
                'amount' => $amount = ($event->amount * $exchangeRate),
                'exchange_rate' => $exchangeRate
            ]);

            $ledger->balance += $amount;
            $ledger->save();
        });
    }

    private function subtractMoneyFromAccountBalance($event, $storedEvent)
    {
        $account = Account::findByEvent($event);

        $account->ledgers->each(function (AccountLedger $ledger) use ($event, $storedEvent) {
            $exchangeRate = $ledger->exchangeRate()->value;

            $ledger->movements()->create([
                'stored_event_id' => $storedEvent->id,
                'stored_event_type' => get_class($event),
                'causer_type' => $event->causerType,
                'causer_id' => $event->causerId,
                'amount' => $amount = ($event->amount * $exchangeRate),
                'exchange_rate' => $exchangeRate
            ]);

            $ledger->balance -= $amount;
            $ledger->save();
        });
    }

    private function addPointsToAccount($event)
    {
        $account = Account::findByEvent($event);

        $account->points += $event->amount;

        $account->saveOrFail();
    }

    private function subtractPointsFromAccount($event)
    {
        $account = Account::findByEvent($event);

        $account->points -= $event->amount;

        $account->saveOrFail();
    }
}
