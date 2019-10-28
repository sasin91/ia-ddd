<?php

namespace App\Domains\Economy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportSum extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'selling_price_excl_tax',
        'selling_price',
        'price_excl_tax',
        'price_incl_tax',
        'tax',
        'diff',
        'ten_percent',
        'ia_excl_tax',
        'ia_incl_tax',
        'ia_final',
        'commission',
        'extra_commission',
        'earned_points'
    ];

    protected $casts = [
        'selling_price_excl_tax' => 'integer',
        'selling_price' => 'integer',
        'price_excl_tax' => 'integer',
        'price_incl_tax' => 'integer',
        'tax' => 'integer',
        'diff' => 'integer',
        'ten_percent' => 'integer',
        'ia_excl_tax' => 'integer',
        'ia_incl_tax' => 'integer',
        'ia_final' => 'integer',
        'commission' => 'integer',
        'extra_commission' => 'integer',
        'earned_points' => 'integer'
    ];

    /**
     * The report this sum belongs to
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
