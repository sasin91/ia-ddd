<?php

namespace App\Domains\Booking\Enums;

use App\Domains\Booking\Tickets\TicketChange;
use BenSampo\Enum\Enum;
use function filled;

final class TicketChangeStatus extends Enum
{
    const NEW = 'new';
    const PENDING = 'pending';
    const COMPLETED = 'completed';

    public static function forModel(TicketChange $change): string
    {
        if (filled($change->completed_at)) {
            return __(self::COMPLETED);
        }

        if (filled($change->confirmed_at)) {
            return __(self::PENDING);
        }

        return __(self::NEW);
    }
}
