<?php

namespace App\Domains\Economy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = [
        'id',
        'total',
        'diff',
        'commission_total',
        'commission_diff'
    ];

    /**
     * Eager Load the latest recorded sums
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeWithLatestSum($query): void
    {
        $query->selectSub(
            'latest_sum_id',
            ReportSum::query()
                ->select('id')
                ->whereColumn('report_id', 'reports.id')
                ->latest()
        )->with('latestSum');
    }

    /**
     * Eager Load the latest recorded totals
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeWithLatestTotal($query): void
    {
        $query->selectSub(
            'latest_total_id',
            ReportTotal::query()
                ->select('id')
                ->whereColumn('report_id', 'reports.id')
                ->latest()
        )->with('latestTotal');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ReportLine::class);
    }

    public function latestSum(): BelongsTo
    {
        return $this->belongsTo(ReportSum::class, 'latest_sum_id');
    }

    public function sums()
    {
        return $this->hasMany(ReportSum::class)->latest();
    }

    public function latestTotal(): BelongsTo
    {
        return $this->belongsTo(ReportTotal::class, 'latest_total_id');
    }

    public function totals()
    {
        return $this->hasMany(ReportTotal::class)->latest();
    }
}
