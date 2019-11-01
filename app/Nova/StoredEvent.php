<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class StoredEvent extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Other';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\StoredEvent::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'event_class';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'event_class'
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
            ID::make(),

            Text::make('aggregate_uuid')->readonly(),

            Text::make('event_class')->readonly(),

            Code::make('event_properties')->readonly(),

            Code::make('meta_data')->readonly(),
        ];
    }
}