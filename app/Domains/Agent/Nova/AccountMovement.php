<?php

namespace App\Domains\Agent\Nova;

use App\Domains\Billing\Nova\Payment;
use App\Domains\Billing\Nova\Revenue;
use App\Nova\Resource;
use App\Nova\StoredEvent;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Number;

class AccountMovement extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Agent';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Agent\Models\AccountMovement::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'stored_event_type'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Ledger', 'ledger', AccountLedger::class),

            BelongsTo::make('Event', 'event', StoredEvent::class),

            MorphTo::make('Causer')->types([
                Revenue::class,
                Payment::class
            ]),

            Currency::make('Amount'),

            Number::make('exchange_rate')
        ];
    }
}