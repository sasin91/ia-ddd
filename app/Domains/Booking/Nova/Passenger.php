<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Domains\Booking\Enums\PassengerGender;
use App\Domains\Booking\Enums\PassengerTitle;
use App\Nova\Resource;
use App\Nova\User;
use Dniccum\PhoneNumber\PhoneNumber;
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
use SimpleSquid\Nova\Fields\Enum\Enum;

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
        'id', 'name'
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

            BelongsTo::make('Creator', 'creator', User::class)
                ->nullable()
                ->sortable()
                ->searchable(),

            BelongsTo::make('Age Group', 'ageGroup', AgeGroup::class)
                ->sortable()
                ->searchable(),


            Enum::make('Title')
                ->rules('required')
                ->attachEnum(PassengerTitle::class)
                ->sortable()
                ->hideFromIndex(),

            Text::make(__('Name'), 'name')
                ->rules('required', 'string')
                ->sortable(),

            Enum::make('Gender')
                ->attachEnum(PassengerGender::class)
                ->rules('required', 'string')
                ->sortable(),

            PhoneNumber::make(__('Phone'), 'phone')
//                ->countries(['DK', 'SWE', 'NO', 'UK', 'IQ'])
//                ->country('AUTO')
                ->disableValidation()
                ->format('### ########')
                ->rules('string', 'required')
                ->sortable(),

            Enum::make(__('Nationality'), 'nationality')
                ->attachEnum(Nationality::class)
                ->rules('required', 'string')
                ->hideFromIndex(),

            Enum::make(__('Citizenship'), 'citizenship')
                ->attachEnum(Citizenship::class)
                ->rules('required', 'string')
                ->hideFromIndex(),

            Date::make(__('Birth date'), 'birthdate')
                ->rules('required', 'date')
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
                ->rules('required_with:passport', 'nullable', 'date', 'after_or_equal:+6 months')
                ->hideFromIndex(),

            Date::make(__('VISA expiration date'), 'visa_expires_at')
                ->rules('required_with:visa', 'nullable', 'date', 'after_or_equal:+6 months')
                ->hideFromIndex(),

            HasMany::make('tickets')
                ->onlyOnDetail()
        ];
    }
}
