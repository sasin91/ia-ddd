<?php

namespace App\Nova;

use App\Domains\Agent\Nova\Account;
use App\Domains\Agent\Nova\Agency;
use App\Domains\Booking\Nova\Ticket;
use App\Domains\Booking\Nova\Passenger;
use App\Domains\Booking\Nova\TicketChange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Vyuldashev\NovaPermission\Permission;
use Vyuldashev\NovaPermission\Role;

class User extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Auth';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email', 'country'
    ];

    /**
     * Build an "index" query for the given resource.
     *
     * @param  NovaRequest  $request
     * @param Builder  $query
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withLastLogin();
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
        return parent::detailQuery($request, $query)->withLastLogin();
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

            Avatar::make(__('Photo'), 'photo_url', 'avatars')
                ->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Username')
                ->sortable()
                ->rules('required', 'max:255'),

            Country::make('Country', 'country_code')
                ->sortable(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            BelongsTo::make('Login', 'lastLogin', Login::class),

            MorphToMany::make('Roles', 'roles', Role::class),
            MorphToMany::make('Permissions', 'permissions', Permission::class),

            HasMany::make('Bookings', 'bookings', Ticket::class)->onlyOnDetail(),
            HasMany::make('Requested Changes', 'requestedChanges', TicketChange::class)->onlyOnDetail(),
            HasMany::make('Handled Changes', 'handledChanges', TicketChange::class)->onlyOnDetail(),
            HasMany::make('Passengers', 'passengers', Passenger::class)->onlyOnDetail(),
            HasMany::make('Agencies', 'agencies', Agency::class)->onlyOnDetail(),
            HasMany::make('Accounts', 'accounts', Account::class)->onlyOnDetail(),
            HasMany::make('Logins', 'logins', Login::class)->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
