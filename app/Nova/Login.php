<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Login extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Auth';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Login::class;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return Date::instance($this->created_at)->toDayDateTimeString();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),

            BelongsTo::make('User'),

            Text::make('IP', 'ip_address')->rules('ip'),
            Text::make('Browser', 'user_agent'),

            DateTime::make('Created', 'created_at')->format('llll'),
            DateTime::make('Updated', 'updated_at')->format('llll')
        ];
    }
}
