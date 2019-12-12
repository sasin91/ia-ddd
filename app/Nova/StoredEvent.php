<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use function get_class;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'event_class'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return Str::humanize($this->event_class);
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Events');
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
            ID::make(),

            Text::make('Aggrgate UUID', 'aggregate_uuid')->readonly()->onlyOnDetail(),

            Text::make('Name', 'event_class')->displayUsing(function ($eventClass) {
                return __(
                    Str::humanize($eventClass)
                );
            })->readonly(),

            Code::make('Properties', 'event_properties')->json()->readonly(),

            Code::make('Meta', 'meta_data')->json()->readonly(),
        ];
    }
}
