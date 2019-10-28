<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Passenger extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Booking';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Booking\Models\Passenger::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'first_name', 'middle_name', 'last_name',
    ];


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('creator')
                ->sortable()
                ->searchable(),

            BelongsTo::make('ageGroup')
                ->sortable()
                ->searchable(),

            Text::make(__('Title'), 'title')
                ->rules('string', 'required')
                ->sortable()
                ->hideFromIndex(),

            Text::make(__('First name'), 'first_name')
                ->rules('required', 'string')
                ->sortable(),

            Text::make(__('Middle name'), 'middle_name')
                ->rules('nullable', 'string')
                ->sortable(),

            Text::make(__('Last name'), 'last_name')
                ->rules('required', 'string')
                ->sortable(),

            Text::make(__('Gender'), 'gender')
                ->rules('required', 'string')
                ->sortable(),

            Number::make(__('Phone'), 'phone')
                ->rules('numeric', 'required')
                ->sortable(),

            Select::make(__('Nationality'), 'nationality')
                ->options(Nationality::toArray())
                ->rules('required', 'string')
                ->hideFromIndex(),

            Select::make(__('Citizenship'), 'citizenship')
                ->options(Citizenship::toArray())
                ->rules('required', 'string')
                ->hideFromIndex(),

            Date::make(__('Birth date'), 'birthdate')
                ->rules('required_with:visa', 'date')
                ->hideFromIndex(),

            Text::make(__('Passport'), 'passport')
                ->rules('nullable', 'string')
                ->hideFromIndex(),

            Text::make(__('VISA'), 'visa')
                ->rules('nullable', 'string')
                ->hideFromIndex(),

            Country::make(__('VISA Country'), 'visa_country')
                ->rules('required_with:visa', 'nullable', 'string')
                ->hideFromIndex(),

            Date::make(__('Passport expiration date'), 'passport_expires_at')
                ->rules('required_with:passport', 'nullable', 'date')
                ->hideFromIndex(),

            Date::make(__('VISA expiration date'), 'visa_expires_at')
                ->rules('required_with:visa', 'nullable', 'date')
                ->hideFromIndex(),

            Date::make(__('VISA issue date'), 'visa_issued_at')
                ->rules('required_with:visa_expires_at', 'nullable', 'date')
                ->hideFromIndex(),

            HasMany::make('tickets')
                ->onlyOnDetail()
        ];
    }
}
