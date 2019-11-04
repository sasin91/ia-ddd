<?php

namespace App\Domains\Booking\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Mdixon18\Fontawesome\Fontawesome;
use function __;

class AgeGroup extends Resource
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
    public static $model = \App\Domains\Booking\Models\AgeGroup::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->rules('required', 'string', 'max:255')
                ->updateRules('unique:age_groups,name,{{resourceId}}')
                ->creationRules('unique:age_groups,name'),

            Fontawesome::make('Icon'),

            Number::make('From'),

            Number::make('To'),

            Boolean::make('passport_required'),

            Number::make('luggage_limit')
        ];
    }
}