<?php


namespace App;

use function now;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Builder createdToday()
 */
class Login extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent'
    ];

    /**
     * Query the logins made today
     *
     * @param Builder $query
     * @return void
     */
    public function scopeCreatedToday($query): void
    {
        $query
            ->where('created_at', '>=', now()->startOfDay())
            ->where('created_at', '<=', now()->endOfDay());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
