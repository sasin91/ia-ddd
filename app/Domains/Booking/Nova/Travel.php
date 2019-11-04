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
use SimpleSquid\Nova\Fields\Enum\Enum;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
       'travel_class', 'flight_number',
    ];

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @return string
     */
    public function title()
    {
        return "{$this->flight_number} {$this->travel_class}";
    }

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

            Text::make('Flight Nr.', 'flight_number')
                ->rules('required', 'string')
                ->sortable(),

            Enum::make('Class', 'travel_class')
                ->creationRules('required')
                ->attachEnum(TravelClass::class)
                ->sortable(),

            BelongsTo::make('Departure', 'departureAirport', Airport::class)
                ->rules('required', 'exists:airports,id')
                ->sortable()
                ->searchable(),

            BelongsTo::make(__('Destination'), 'destinationAirport', Airport::class)
                ->rules('required', 'exists:airports,id')
                ->sortable()
                ->searchable(),

            HasMany::make('Times', 'times', TravelTime::class)->onlyOnDetail(),
            HasMany::make('Seats', 'seats', Seat::class)->onlyOnDetail(),
            HasMany::make('Stopovers', 'stopovers', TravelStopover::class)->onlyOnDetail(),
            HasMany::make('Changes', 'changes', TravelChange::class)->onlyOnDetail(),
            HasMany::make('Cancels', 'cancels', TravelCancel::class)->onlyOnDetail(),
            HasMany::make('Outward Tickets', 'outwardTickets', Ticket::class)->onlyOnDetail(),
            HasMany::make('Home Tickets', 'homeTickets', Ticket::class)->onlyOnDetail(),
        ];
    }
}
