<?php

namespace App\Domains\Booking\Contracts;

interface Changeable
{
    /**
     * Determine the fee for changing the model
     *
     * @param array $changes
     * @return int
     */
    public function determineChangeCost(array $changes): int;

    /**
     * Apply the changes to the model
     *
     * @param array $changes
     * @return $this
     */
    public function applyChanges(array $changes): Changeable;
}
