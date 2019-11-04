<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Aero\Nova\AeroAction;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TravelPeriod;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Ticket extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Booking';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['booking:PNR', 'passenger:title,first_name,middle_name,last_name'];

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Booking\Models\Ticket::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'ticket_period'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        $title = '';

        if ($this->booking) {
            $title .= "[$this->booking->PNR]";
        }

        if ($this->passenger) {
            $title .= $this->passenger->full_name;
        }

        return $title;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Booking', 'booking', Booking::class),

            BelongsTo::make('Passenger', 'passenger', Passenger::class),

            BelongsTo::make('Price', 'price', TicketPrice::class),

            Select::make('Travel Period', 'period')
                ->options(TravelPeriod::toSelectArray()),

            Select::make('Travel Class', 'travel_class')
                ->options(TravelClass::toSelectArray())
                ->hideFromIndex(),

            Text::make('Outward Flight Nr.', 'outward_flight_number')
                ->hideFromIndex(),

            DateTime::make('Outward Departure', 'outward_departure_datetime')
                ->hideFromIndex(),

            DateTime::make('Outward Arrival', 'outward_arrival_datetime')
                ->hideFromIndex(),

            Text::make('Home Flight Nr.', 'home_flight_number')
                ->hideFromIndex(),

            DateTime::make('Home Departure', 'home_departure_datetime')
                ->hideFromIndex(),

            DateTime::make('Home Arrival', 'home_arrival_datetime')
                ->hideFromIndex(),

            HasMany::make('Actions', 'aeroActions', AeroAction::class)
                ->onlyOnDetail(),

            HasMany::make('Changes', 'ticketChanges', TicketChange::class)
                ->onlyOnDetail(),
        ];
    }
}
