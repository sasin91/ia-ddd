<?php

namespace App\Domains\Billing;

use App\Collections\FileCollection;
use App\Domains\Billing\Contracts\BillingMethod as BillingMethodContract;
use App\Domains\Billing\Exceptions\BillingMethodNotFound;
use App\Filesystem\FileIndex;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use function app;
use function resolve;

class BillingMethod
{
    /**
     * The registered billing methods
     *
     * @var FileCollection
     */
    protected static $discovered;

    /**
     * Scan and register the billing methods
     *
     * @param Application|null $app
     * @return void
     */
    public static function discover(?Application $app): void
    {
        $app = $app ?? app();

        static::$discovered = FileIndex::scan('Domains/Billing/Methods')->each(function ($billingMethod) use ($app) {
            $app->singleton($billingMethod->class);
        });
    }

    /**
     * Get the billing methods as an array formatted for a select.
     *
     * [string $name => string $name]
     *
     * @return array
     */
    public static function toSelectArray(): array
    {
        return static::$discovered->map(function ($billingMethod) {
            return [$billingMethod->name => $billingMethod->name];
        })->toArray();
    }

    /**
     * Resolve an instance of a billing method from it's bound class name or alias
     *
     * @param string $method
     * @throws BillingMethodNotFound
     * @return BillingMethodContract
     */
    public static function make($method)
    {
        $billingMethod = static::$discovered->first(function ($billingMethod) use ($method) {
            return $billingMethod->class === $method
                || $billingMethod->name === Str::studly($method);
        });

        if ($billingMethod === null) {
            throw BillingMethodNotFound::make($method);
        }

        return resolve($billingMethod->class);
    }
}
