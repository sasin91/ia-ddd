<?php

namespace App\Domains\Agent\Nova;

use App\Domains\Booking\Nova\Travel;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

            BelongsTo::make('Account', 'account', Account::class)->rules('required'),

            BelongsTo::make('Travel', 'travel', Travel::class)
                ->rules('required')
                ->creationRules('unique:commissions,travel_id')
                ->updateRules(Rule::unique('commissions', 'travel_id')->ignore('{{resourceId}}')->where(function ($query) {
                    $query->where('account_id', '!=', $this->model()->account_id);
                })),

            Currency::make('Base', 'base')->withMeta(['value' => 75]),

            Number::make('Extra %', 'extra')->withMeta(['value' => 0]),

            Number::make('Points %', 'points_percentage')->withMeta(['value' => 0.8])->step(0.01)
        ];
    }
}