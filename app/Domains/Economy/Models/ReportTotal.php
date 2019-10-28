<?php

namespace App\Domains\Economy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTotal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'sita_total',
        'sita_tax',
        'sita_price',
        'sita_commission',
        'commission',
        'extra_commission',
        'commission_diff',
        'net',
        'emd_refunds',
        'tax_refunds',
        'voided_docs',
        'voided_system',
        'agent_points'
    ];

    protected $casts = [
        'sita_total' => 'integer',
        'sita_tax' => 'integer',
        'sita_price' => 'integer',
        'sita_commission' => 'integer',
        'commission' => 'integer',
        'extra_commission' => 'integer',
        'commission_diff' => 'integer',
        'net' => 'integer',
        'emd_refunds' => 'integer',
        'tax_refunds' => 'integer',
        'voided_docs' => 'integer',
        'voided_system' => 'integer',
        'agent_points' => 'integer'
    ];

    /**
     * The report this total belongs to
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
