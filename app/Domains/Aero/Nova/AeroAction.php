<?php


namespace App\Domains\Aero\Nova;

use App\Domains\Aero\Enums\AeroActionType;
use App\Domains\Booking\Nova\Trip;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use SimpleSquid\Nova\Fields\Enum\Enum;

use function __;

class AeroAction extends Resource
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
    public static $model = \App\Domains\Aero\Models\AeroAction::class;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Airport Terminal Actions log');
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

            BelongsTo::make('Ticket', 'ticket', Trip::class),

            BelongsTo::make(Aero::label(), 'aero', Aero::class),

            Enum::make('Type')
                ->attachEnum(AeroActionType::class),

            Text::make('Command')
                ->hideFromIndex(),

            Text::make('E-Ticket', 'e_ticket')
                ->hideFromIndex(),

            Currency::make('Tax'),

            Currency::make('Price')
        ];
    }
}
