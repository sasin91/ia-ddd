<?php

namespace App\Domains\Booking\Nova;

use App\Nova\Resource;
use App\Nova\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class TicketChange extends Resource
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
    public static $model = \App\Domains\Booking\Models\TicketChange::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        //
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('status')->readonly(),

            BelongsTo::make('Handled by', 'handledBy', User::class),

            BelongsTo::make('Requested by', 'requestedBy', User::class),

            BelongsTo::make('Ticket', 'ticket', Ticket::class),

            Currency::make('Fee'),

            Currency::make('Cost'),

            Currency::make('Diff'),

            Code::make('Before'),

            Code::make('After'),

            DateTime::make('Confirmed', 'confirmed_at'),

            DateTime::make('Completed', 'completed_at')
        ];
    }
}
