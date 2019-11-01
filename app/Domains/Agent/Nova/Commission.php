<?php

namespace App\Domains\Agent\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;

class Commission extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Agent';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Agent\Models\Commission::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'stored_event_type'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return "[{$this->travel->flight_number}|{$this->travel->travel_class}]";
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     */
    public function subtitle()
    {
        return "{$this->base} / {$this->extra}";
    }

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

            BelongsTo::make('Account'),

            BelongsTo::make('Travel'),

            Currency::make('base'),

            Number::make('extra'),

            Number::make('points_percentage')
        ];
    }
}