<?php

namespace App\Domains\Billing\Nova;

use App\Domains\Agent\Nova\Account;
use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Enums\ExpenseCategory;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use SimpleSquid\Nova\Fields\Enum\Enum;

use function config;

class Expense extends Resource
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
    public static $model = \App\Domains\Billing\Models\Expense::class;

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

            BelongsTo::make('Revenue', 'revenue', Revenue::class)->nullable(),

            Text::make('Email', 'customer_email')->rules('email'),

            Currency::make('Amount'. 'amount'),

            Number::make('Points', 'points'),

            Number::make('Exchange Rate', 'exchange_rate'),

            Select::make('Currency', 'currency_code')
                ->options(config('currency.supported')),

            Text::make('Description', 'description'),

            Enum::make('Category', 'category')
                ->attachEnum(ExpenseCategory::class),

            Select::make('Method', 'billing_method')
                ->options(BillingMethod::toSelectArray()),

            Text::make('Reference', 'reference'),

            DateTime::make('Paid', 'paid_at')
        ];
    }
}
