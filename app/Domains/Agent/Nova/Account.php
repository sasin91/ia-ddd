<?php

namespace App\Domains\Agent\Nova;

use App\Domains\Agent\Enums\AccountType;
use App\Nova\Resource;
use App\Nova\StoredEvent;
use App\Nova\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\Enum\Enum;
use function config;
use function transform;

class Account extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Agent';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [
        'owner:name',
        'agency:name'
    ];

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Agent\Models\Account::class;

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
     * Build an "index" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param Builder  $query
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->editing) {
            return $query;
        }

        return $query->withBalance(
            Session::get('currency', config('currency.default'))
        );
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query)->withBalance(
            Session::get('currency', config('currency.default'))
        );
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        if ($this->owner && $this->agency) {
            return "[{$this->owner->name}@{$this->agency->name}] {$this->type->value}";
        }

        return $this->type->value;
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     */
    public function subtitle()
    {
        return $this->uuid;
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

            Currency::make('Balance')
                ->readOnly()
                ->exceptOnForms(),

            Enum::make('Type')
                ->sortable()
                ->attachEnum(AccountType::class),

            Text::make('uuid')->readonly(),

            Text::make('description')->hideFromIndex(),

            HasMany::make('Events', 'events', StoredEvent::class),
            HasMany::make('Ledgers', 'ledgers', AccountLedger::class),
            HasMany::make('Commissions', 'commissions', Commission::class)
        ];
    }
}
