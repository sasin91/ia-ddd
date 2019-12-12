<?php

namespace App\Domains\Booking\Nova;

use App\Domains\Booking\Enums\TripType;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use function config;

class Price extends Resource
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
    public static $model = \App\Domains\Booking\Models\Price::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'type', 'currency'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return "[{$this->season->name} {$this->type}]: {$this->amount} {$this->currency}";
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

            BelongsTo::make('Travel', 'travel', Travel::class),

            BelongsTo::make('Season', 'season', PriceSeason::class),

            BelongsTo::make('Age Group', 'ageGroup', AgeGroup::class),

            Select::make('Period', 'type')
                ->options(TripType::toSelectArray()),

            Select::make('Currency', 'currency')
                ->options(config('currency.supported')),

            Currency::make('Amount', 'amount')
        ];
    }
}
