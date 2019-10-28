<?php

namespace App\Domains\Agent\Events;

use Spatie\EventSourcing\ShouldBeStored;

class CreateAccount implements ShouldBeStored
{
    /**
     * Account attrs
     *
     * @var array
     */
    public $attributes = [];

    /**
     * StartAccount constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
