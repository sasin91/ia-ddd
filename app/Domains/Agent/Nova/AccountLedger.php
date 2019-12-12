<?php

namespace App\Domains\Agent\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use function config;

class AccountLedger extends Resource
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
    public static $model = \App\Domains\Agent\Models\AccountLedger::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'currency';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'currency'
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

            BelongsTo::make('Account', 'account', Account::class),

            Select::make('Currency')->options(config('currency.supported')),

            Currency::make('Balance')->rules('numeric'),

            HasMany::make('Movements', 'movements', AccountMovement::class)->onlyOnDetail()
        ];
    }
}
