<?php

namespace App\Domains\Aero\Enums;

use BenSampo\Enum\Enum;

final class AeroActionType extends Enum
{
    const ADDED = 'A';
    const MODIFIED = 'M';
    const ERASED = 'E';
}
