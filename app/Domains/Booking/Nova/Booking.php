<?php

namespace App\Domains\Booking\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Booking extends Resource
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
    public static $model = \App\Domains\Booking\Models\Booking::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'PNR';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'PNR', 'buyer_email'
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
            ID::make()
                ->sortable(),

            Text::make('PNR', 'PNR')
                ->sortable(),

            Text::make('buyer_email')
                ->sortable(),

            BelongsTo::make('buyer')
                ->sortable(),

            Boolean::make('express'),

            Currency::make('total_cost'),

            DateTime::make('voided_at'),

            DateTime::make('documents_sent_at'),

            HasMany::make('tickets')
                ->onlyOnDetail(),
        ];
    }
}
