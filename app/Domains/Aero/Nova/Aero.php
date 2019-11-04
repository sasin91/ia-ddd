<?php

namespace App\Domains\Aero\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Aero extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Aero';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Aero\Models\Aero::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Airport Terminal');
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

            Text::make('Name')
                ->rules('required', 'max:254'),

            Text::make('IP Address', 'terminal_ip')
                ->rules('ip', 'max:254'),

            Text::make('Emulator', 'terminal_emulator')
                ->rules('max:254'),

            HasMany::make('Actions', 'actions', AeroAction::class)->onlyOnDetail()
        ];
    }
}
