<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Booking\Enums\TravelClass;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use function __;

class Travel extends Resource
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
    public static $model = \App\Domains\Booking\Models\Travel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'flight_number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'flight_number',
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

            Text::make('flight_number')
                ->rules('required', 'string')
                ->sortable(),

            Select::make('travel_class')
                ->creationRules('required')
                ->options(TravelClass::toSelectArray())
                ->sortable(),

            BelongsTo::make(__('Departure'), 'departureAirport')
                ->rules('required', 'exists:airports,id')
                ->sortable()
                ->searchable(),

            BelongsTo::make(__('Destination'), 'destinationAirport')
                ->rules('required', 'exists:airports,id')
                ->sortable()
                ->searchable(),

            HasMany::make('Seats')->onlyOnDetail(),
            HasMany::make('Times')->onlyOnDetail(),
            HasMany::make('Stopovers')->onlyOnDetail(),
            HasMany::make('Changes')->onlyOnDetail(),
            HasMany::make('Cancels')->onlyOnDetail(),
            HasMany::make('Tickets')->onlyOnDetail(),
        ];
    }
}
