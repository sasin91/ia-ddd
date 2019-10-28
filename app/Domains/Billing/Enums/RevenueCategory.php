<?php

namespace App\Domains\Billing\Enums;

use BenSampo\Enum\Enum;

final class RevenueCategory extends Enum
{
    const DEPOSITS = 'deposits';
    const PURCHASES = 'purchases';
    const BANK_TRANSFERS = 'bank transfers';
    const CASH = 'cash';
}
