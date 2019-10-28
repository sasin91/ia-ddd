<?php

namespace App\Domains\Booking\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'flight_number',
        'departs_at',
        'remaining',
        'available',
        'occupied',
        'by_others',
        'by_us',
        'tickets_count'
    ];

    protected $casts = [
        'departs_at' => 'datetime'
    ];

    /**
     * Find or create a matching seat
     *
     * @param string $flightNumber
     * @param string|DateTimeInterface $departsAt
     * @return Seat
     */
    public static function resolve(string $flightNumber, $departsAt)
    {
        return static::query()
            ->where([
                'flight_number' => $flightNumber,
                'departs_at' => $departsAt
            ])
            ->firstOr(function () use ($flightNumber, $departsAt) {
                $seatsCount = Travel::query()->where('flight_number', $flightNumber)->value('default_seats') ?? 159;

                return static::query()->create([
                    'flight_number' => $flightNumber,
                    'departs_at' => $departsAt,
                    'remaining' => $seatsCount,
                    'available' => $seatsCount,
                    'occupied' => 0,
                    'by_others' => 0,
                    'by_us' => 0,
                    'tickets_count' => 0
                ]);
            });
    }

    public function travel()
    {
        return $this->belongsTo(Travel::class, 'flight_number', 'flight_number');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'flight_number', 'flight_number');
    }

    /**
     * Reserve an amount of seats
     *
     * @param integer $amount
     * @param string $type
     * @return $this
     */
    public function reserve(int $amount = 1, string $type = 'by_us')
    {
        $this->increment($type, $amount);

        if ($type === 'by_us') {
            $this->increment('tickets_count', $amount);
        }

        $this->increment('occupied', $amount);

        $this->decrement('remaining', $amount);

        return $this;
    }

    /**
     * Release an amount of reserved seats
     *
     * @param integer $amount
     * @param string $type
     * @return $this
     */
    public function release(int $amount = 1, string $type = 'by_us')
    {
        $this->decrement($type, $amount);

        if ($type === 'by_us') {
            $this->decrement('tickets_count', $amount);
        }

        $this->decrement('occupied', $amount);

        $this->increment('remaining', $amount);

        return $this;
    }
}
