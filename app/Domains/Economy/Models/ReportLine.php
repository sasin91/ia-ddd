<?php

namespace App\Domains\Economy\Models;

use App\Domains\Booking\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ReportLine
 * @package App\Domains\Economy\Models
 *
 * @property integer $report_id
 * @property integer $ticket_id
 * @property integer $aero_action_id
 * @property integer $selling_price
 * @property integer $buying_price
 * @property integer $tax
 * @property integer $ten_percent
 * @property integer $base_commission
 * @property integer $extra_commission
 * @property boolean $verified_by_reception
 * @property boolean $verified_by_accounting
 */
class ReportLine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'ticket_id',
        'aero_action_id',

        'selling_price',
        'buying_price',
        'tax',

        'ten_percent',
        'base_commission',
        'extra_commission',

        'verified_by_reception',
        'verified_by_accounting',
    ];

    protected $casts = [
        'verified_by_reception' => 'boolean',
        'verified_by_accounting' => 'boolean'
    ];

    /**
     * The report this line appears on
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
