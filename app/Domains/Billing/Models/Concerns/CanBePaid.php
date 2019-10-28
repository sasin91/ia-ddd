<?php

namespace App\Domains\Billing\Models\Concerns;

use DateTimeInterface;
use function defined;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait CanBePaid
 *
 * @property DateTimeInterface|null $paid_at
 * @method static Builder paid($atDate = null)
 * @method static Builder unpaid($atDate = null)
 */
trait CanBePaid
{
    /**
     * Initialize the soft deleting trait for an instance.
     *
     * @return void
     */
    public function initializeCanBePaid()
    {
        $this->dates[] = $this->getPaidAtColumn();
    }

    /**
     * Get the name of the "paid at" column.
     *
     * @return string
     */
    public function getPaidAtColumn(): string
    {
        return defined('static::PAID_AT') ? static::PAID_AT : 'paid_at';
    }

    /**
     * Mark the model as paid
     *
     * @param DateTimeInterface|string|null $date
     * @return $this
     * @throws \Throwable
     */
    public function markAsPaid($date = null)
    {
        $this
            ->forceFill([$this->getPaidAtColumn() => $this->asDateTime($date)])
            ->saveOrFail();

        return $this;
    }

    /**
     * Query the paid models
     *
     * @param Builder $query
     * @param DateTimeInterface|string|null $atDate
     */
    public function scopePaid($query, $atDate = null)
    {
        $query->withoutGlobalScope('unpaid');

        if ($atDate) {
            $query
                ->where($this->getPaidAtColumn(), '>=', $this->asDateTime($atDate)->startOfDay())
                ->where($this->getPaidAtColumn(), '<=', $this->asDateTime($atDate)->endOfDay());
        } else {
            $query->whereNotNull($this->getPaidAtColumn());
        }
    }

    /**
     * Query the unpaid payments
     *
     * @param Builder $query
     * @param DateTimeInterface|string|null $atDate
     */
    public function scopeUnpaid($query, $atDate = null)
    {
        $query->withoutGlobalScope('paid');

        if ($atDate) {
            $query
                ->where('created_at', '>=', $this->asDateTime($atDate)->startOfDay())
                ->where('created_at', '<=', $this->asDateTime($atDate)->endOfDay());
        } else {
            $query->whereNull($this->getPaidAtColumn());
        }
    }

    /**
     * Whether the model has been paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->{$this->getPaidAtColumn()} !== null;
    }

    /**
     * Whether the model is unpaid
     *
     * @return bool
     */
    public function isUnpaid(): bool
    {
        return !$this->isPaid();
    }
}
