<?php


namespace App\Domains\Billing\Models;


use App\Domains\Agent\Models\Commission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Discount
 * @package App\Domains\Billing\Models
 *
 * @property string $discountable_type
 * @property integer $discountable_id
 * @property string $discounted_type
 * @property integer $discounted_id
 * @property boolean $active
 * @property integer $amount
 *
 * @property-read Commission $discountable
 * @property-read Revenue $discounted
 *
 * @method static Builder|Discount active()
 * @method static Builder|Discount inactive()
 */
class Discount extends Model
{
    protected $fillable = [
        'discountable_type',
        'discountable_id',
        'discounted_type',
        'discounted_id',
        'active',
        'amount'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Query the active discounts
     *
     * @param Builder|Discount $query
     */
    public function scopeActive($query): void
    {
        $query->withoutGlobalScope('inactive');

        $query->where('active', true);
    }

    /**
     * Query the inactive discounts
     *
     * @param Builder|Discount $query
     */
    public function scopeInactive($query): void
    {
        $query->withoutGlobalScope('active');

        $query->where('active', false);
    }

    /**
     * The discountable model
     *
     * @return MorphTo<Commission>
     */
    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The model the discount applies to
     *
     * @return MorphTo<Revenue>
     */
    public function discounted(): MorphTo
    {
        return $this->morphTo();
    }
}
