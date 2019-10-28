<?php


namespace App\Domains\Billing\Exceptions;

use InvalidArgumentException;

class BillingMethodNotFound extends InvalidArgumentException
{
    /**
     * Create a new exception instance
     *
     * @param string $method
     * @return BillingMethodNotFound
     */
    public static function make($method)
    {
        return new static("{$method} is not supported.");
    }
}
