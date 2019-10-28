<?php

namespace App\Domains\Billing\Enums;

use BenSampo\Enum\Enum;

final class PaymentCategory extends Enum
{
    const BILL = 'bill';
    const REFUND = 'refund';
    const COMMISSION = 'base commission';
    const EXTRA_COMMISSION = 'extra commission';
    const POINTS_PURCHASE = 'points purchase';
    const POINTS_REFUND = 'points refund';
    const POINTS_DEPOSIT = 'points deposit';
}
