<?php

namespace App\Domains\Booking\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;

class TravelTime extends Resource
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
    public static $model = \App\Domains\Booking\Models\TravelChange::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'weekday';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'weekday'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Travel', 'travel', Travel::class)
                ->rules('required'),

            Select::make('Weekday', 'weekday')
                ->options(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])
                ->rules('required'),

            Number::make('Departure Time', 'departure_time')
                ->rules('date_format:H:i'),

            Number::make('Arrival Time', 'arrival_time')
                ->rules('date_format:H:i')
        ];
    }
}
