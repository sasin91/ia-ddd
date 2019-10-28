<?php

namespace App\Support;

use App\Collections\DateRangeCollection;
use Countable;
use DateInterval;
use DatePeriod;
use function iterator_to_array;
use Illuminate\Support\Carbon;
use IteratorAggregate;

class DateRange implements IteratorAggregate, Countable
{
    /**
     * The start of the date range.
     *
     * @var Carbon
     */
    protected $starts_at;

    /**
     * The end of the date range.
     *
     * @var Carbon
     */
    protected $ends_at;

    /**
     * The space between the dates.
     *
     * @var \DateInterval
     */
    protected $interval;

    /**
     * The date format.
     *
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * Key of the dates.
     *
     * @var string
     */
    protected $keyedBy;

    /**
     * Static constructor
     *
     * @return DateRange
     */
    public static function make()
    {
        return new static(...func_get_args());
    }

    /**
     * Collect all the dates between start and end
     *
     * @param string $starts_at
     * @param string $ends_at
     * @return  DateRangeCollection
     */
    public static function collect($starts_at, $ends_at)
    {
        return (new static($starts_at, $ends_at, '1 day'))->create();
    }

    /**
     * Construct the DateRange.
     *
     * @param  string|null          $starts_at [defaults to now.]
     * @param  string|null          $ends_at [defaults to end of year.]
     * @param  string|null          $interval [The interval between each date.]
     */
    public function __construct($starts_at = null, $ends_at = null, $interval = null)
    {
        $this->startsAt($starts_at ?: Carbon::now());
        $this->endsAt($ends_at ?: Carbon::now()->endOfYear());
        $this->interval($interval ?: '1 day');
    }

    /**
     * Delegate calls to the underlying collection.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed | \Illuminate\Support\Collection
     */
    public function __call($method, $parameters = [])
    {
        return $this->create()->$method(...$parameters);
    }

    /**
     * Delegate undefined attributes to the underyling Collection.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return $this->create()->$key;
    }

    /**
     * Assign a key for each of the dates
     *
     * @param  string $key
     * @return $this
     */
    public function keyedBy(string $key)
    {
        $this->keyedBy = $key;

        return $this;
    }

    /**
     * The date format
     *
     * @param  string $format
     * @return $this
     */
    public function format(string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Set the start date
     *
     * @param string|\DateTime $date
     * @return $this
     */
    public function startsAt($date)
    {
        $this->starts_at = Carbon::parse($date);

        return $this;
    }

    /**
     * Set the end date
     *
     * @param string|\DateTime $date
     * @return $this
     */
    public function endsAt($date)
    {
        $this->ends_at = Carbon::parse($date);

        return $this;
    }

    /**
     * Set the date interval
     *
     * @param  DateInterval|string $value
     * @return $this
     */
    public function interval($value)
    {
        if (is_string($value)) {
            $this->interval = DateInterval::createFromDateString($value);
        } else {
            $this->interval = $value;
        }

        return $this;
    }

    /**
     * Create the dateRange collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function create()
    {
        $dates = $this->formatDates($this->dates());

        $results = $this->assignKeyToDates($dates);

        return new DateRangeCollection($results);
    }

    /**
     * Get the raw date array
     *
     * @return array
     */
    public function dates()
    {
        if ($this->starts_at->isSameDay($this->ends_at)) {
            return [
                $this->starts_at
            ];
        }

        return iterator_to_array(new DatePeriod(
            $this->starts_at,
            $this->interval,
            (clone $this->ends_at)->addDay()
        ));
    }

    /**
     * Get the iterator from the collection
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->create()->getIterator();
    }

    /**
     * Count the total number of dates.
     *
     * @return int
     */
    public function count()
    {
        return count($this->dates());
    }

    /**
     * Format the DateTime objects into strings.
     *
     * @param  array  $range
     * @return array
     */
    protected function formatDates(array $range)
    {
        if (blank($this->format)) {
            return $range;
        }

        return array_map(function ($date) {
            return $date->format($this->format);
        }, $range);
    }

    /**
     * Assign the key to each of the dates
     *
     * @param  array  $dates
     * @return array
     */
    protected function assignKeyToDates(array $dates)
    {
        if (blank($this->keyedBy)) {
            return $dates;
        }

        $results = [];

        foreach ($dates as $date) {
            $results[] = [$this->keyedBy => $date];
        }

        return $results;
    }
}
