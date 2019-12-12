<?php

namespace App\Providers;

use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Models\Expense;
use App\Domains\Billing\Models\Revenue;
use App\Domains\Booking\Models\Ticket;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use function class_basename;
use function explode;
use function get_class;
use function is_object;
use function last;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        BillingMethod::discover($this->app);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('addSubSelect', function ($query, $column) {
            if (is_null($this->columns)) {
                $this->select($this->from.'.*');
            }

            return $this->selectSub($query, $column);
        });

        Str::macro('humanize', function ($value) {
            if (is_object($value)) {
                return Str::humanize(class_basename(get_class($value)));
            }

            return Str::title(
                Str::snake(last(
                    explode('\\', $value)
                ), ' ')
            );
        });

        Relation::morphMap([
            'Revenue' => Revenue::class,
            'Expense' => Expense::class,
            'Ticket' => Ticket::class
        ]);
    }
}
