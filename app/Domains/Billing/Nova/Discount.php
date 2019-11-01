<?php

namespace App\Domains\Billing\Nova;

use App\Domains\Billing\Models\Concerns\Discountable;
use App\Domains\Billing\Models\Concerns\HasDiscounts;
use App\Filesystem\FileIndex;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use function app_path;

class Discount extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Billing';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Domains\Billing\Models\Discount::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'category', 'customer_email', 'reference'
    ];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return "{$this->discountable_type} for {$this->discounted_type}";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fileIndex = FileIndex::scan(app_path())->whereClassExists();

        return [
            ID::make()->sortable(),

            MorphTo::make('Discounted')->types(
                (clone $fileIndex)
                    ->whereUsing(HasDiscounts::class)
                    ->mapToNovaResources()
                    ->toArray()
            ),

            MorphTo::make('Discountable')->types(
                (clone $fileIndex)
                    ->whereUsing(Discountable::class)
                    ->mapToNovaResources()
                    ->toArray()
            ),

            Boolean::make('active'),

            Currency::make('amount')
        ];
    }
}
