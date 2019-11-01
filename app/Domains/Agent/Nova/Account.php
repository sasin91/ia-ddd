<?php

namespace App\Domains\Agent\Nova;

use App\Domains\Agent\Enums\AccountType;
use App\Nova\Resource;
use App\Nova\StoredEvent;
use App\Nova\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Account extends Resource
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
    public static $model = \App\Domains\Agent\Models\Account::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'type';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'type', 'uuid', 'description'
    ];

    /**
     * Determine if the current user can create new resources.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        // Accounts should be created through Event Sourcing
        return false;
    }

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

            BelongsTo::make('Owner', 'owner', User::class),
            BelongsTo::make('Agency'),

            Number::make('Points')
                ->min(0)
                ->max(2147483647),

            Select::make('Type')
                ->sortable()
                ->options(AccountType::toSelectArray()),

            Text::make('uuid')->readonly(),

            Text::make('description')->hideFromIndex(),

            HasMany::make('Events', 'events', StoredEvent::class),
            HasMany::make('Ledgers', 'ledgers', AccountLedger::class)
        ];
    }
}