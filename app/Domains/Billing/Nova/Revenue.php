<?php

namespace App\Domains\Billing\Nova;

use App\Domains\Agent\Nova\Account;
use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use function config;

class Revenue extends Resource
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
    public static $model = \App\Domains\Billing\Models\Revenue::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'category';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'category', 'customer_email', 'reference'
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
            ID::make()->sortable(),

            BelongsTo::make('Account', 'account', Account::class),

            MorphMany::make('Discounts', 'discounts', Discount::class),

            Text::make('Email', 'customer_email')->rules('email'),

            Currency::make('amount'),

            Number::make('earned_points'),

            Number::make('exchange_rate'),

            Select::make('currency_code')
                ->options(config('currency.supported')),

            Text::make('description'),

            Select::make('category')
                ->options(RevenueCategory::toSelectArray()),

            Select::make('billing_method')
                ->options(BillingMethod::toSelectArray()),

            Text::make('reference'),

            DateTime::make('refunded_at'),

            DateTime::make('paid_at')
        ];
    }
}
