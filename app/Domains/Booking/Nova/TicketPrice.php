<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Booking\Enums\TicketPeriod;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use function config;

class TicketPrice extends Resource
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
    public static $with = ['season:name'];


    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Booking\Models\TicketPrice::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'ticket_period', 'currency'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return "[{$this->season->name} {$this->ticket_period}]: {$this->amount} {$this->currency}";
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

            BelongsTo::make('departure'),

            BelongsTo::make('season'),

            BelongsTo::make('ageGroup'),

            Select::make('ticket_period')
                ->options(TicketPeriod::toSelectArray()),

            Select::make('currency')
                ->options(config('currency.supported')),

            Currency::make('amount')
        ];
    }
}
