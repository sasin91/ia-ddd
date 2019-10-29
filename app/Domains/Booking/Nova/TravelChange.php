<?php

namespace App\Domains\Booking\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class TravelChange extends Resource
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
    public static $title = 'flight_number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'flight_number'
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

            Text::make('flight_number')
                ->rules('required', 'string')
                ->sortable(),

            BelongsTo::make('departure'),
        ];
    }
}