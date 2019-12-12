<?php

namespace App\Domains\Booking\Nova;

use DateTimeZone;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use function collect;

class Airport extends Resource
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
    public static $model = \App\Domains\Booking\Models\Airport::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'IATA';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'IATA',
        'location',
        'country'
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

            Text::make('IATA', 'IATA')
                ->rules('required', 'string')
                ->creationRules('unique:airports,IATA')
                ->updateRules('unique:airports,IATA,{{resourceId}}')
                ->sortable(),

            Text::make(__('Location'), 'location')->rules('required', 'string')->sortable(),

            Country::make(__('Country'), 'country')->rules('required', 'string')->sortable(),

            Select::make(__('Timezone'), 'timezone')
                ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(function ($timezone) {
                    return [$timezone => $timezone];
                })->toArray())
                ->rules('required', 'string')
                ->sortable(),

            HasMany::make(__('Departures'), 'departures', Travel::class)->rules('exists:travels,id'),
            HasMany::make(__('Arrivals'), 'arrivals', Travel::class)->rules('exists:travels,id'),
        ];
    }
}
