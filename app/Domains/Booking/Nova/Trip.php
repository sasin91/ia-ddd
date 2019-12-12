<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Aero\Nova\AeroAction;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TripType;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use SimpleSquid\Nova\Fields\Enum\Enum;

class Trip extends Resource
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
    public static $with = ['ticket:PNR', 'passenger:title,name'];

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Booking\Models\Trip::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'type'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        $title = '';

        if ($this->ticket) {
            $title .= "[{$this->ticket->PNR}] ";
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

            BelongsTo::make('Ticket', 'ticket', Ticket::class),

            BelongsTo::make('Passenger', 'passenger', Passenger::class),

            BelongsTo::make('Price', 'price', Price::class),

            BelongsTo::make('Outward', 'outwardTravel', Travel::class),

            BelongsTo::make('Home', 'homeTravel', Travel::class),

            Enum::make('Travel Period', 'type')
                ->attachEnum(TripType::class),

            Enum::make('Travel Class', 'travel_class')
                ->attachEnum(TravelClass::class)
                ->hideFromIndex(),

            DateTime::make('Outward Departure', 'outward_departure_datetime')
                ->hideFromIndex()
                ->format('llll'),

            DateTime::make('Outward Arrival', 'outward_arrival_datetime')
                ->hideFromIndex()
                ->format('llll'),

            DateTime::make('Home Departure', 'home_departure_datetime')
                ->hideFromIndex()
                ->format('llll'),

            DateTime::make('Home Arrival', 'home_arrival_datetime')
                ->hideFromIndex()
                ->format('llll'),

            HasMany::make('Actions', 'aeroActions', AeroAction::class)
                ->onlyOnDetail(),

            HasMany::make('Changes', 'ticketChanges', TicketChange::class)
                ->onlyOnDetail(),
        ];
    }
}
