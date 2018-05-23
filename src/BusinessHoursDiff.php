<?php

namespace Raupp;

use Carbon\Carbon;

/**
 * Class BusinessHoursDiff
 *
 * @package RauppRafael\BusinessHoursDiff
 * @author Rafael Raupp
 */
class BusinessHoursDiff
{
    /**
     * Time in hours that the business opens
     *
     * @var int
     */
    protected $businessOpensAt;

    /**
     * Time in hours that the business opens
     *
     * @var int
     */
    protected $businessClosesAt;

    /**
     * The unit to which result should be converted to
     *
     * @var string
     */
    protected $unit;

    /**
     * BusinessHoursDiff constructor.
     *
     * @param int $businessStart
     * @param int $businessEnd
     * @param string $unit
     */
    public function __construct(int $businessStart = null, int $businessEnd = null, string $unit = 'min')
    {
        $this->businessOpensAt = $businessStart;
        $this->businessClosesAt = $businessEnd;
        $this->unit = $unit;
    }

    /**
     * Sets the time that the business opens
     *
     * @param int $businessOpensAt
     * @return $this
     */
    public function businessOpensAt(int $businessOpensAt)
    {
        $this->businessOpensAt = $businessOpensAt;
        return $this;
    }

    /**
     * Sets the time that the business closes
     *
     * @param int $businessClosesAt
     * @return $this
     */
    public function businessClosesAt(int $businessClosesAt)
    {
        $this->businessClosesAt = $businessClosesAt;
        return $this;
    }

    /**
     * Sets the unit that the value should return
     *
     * Available units:
     * min
     *
     * @param string $unit
     * @return $this
     */
    public function unit(string $unit)
    {
        $availableUnits = ['min', 'minutes'];

        if (in_array($unit, $availableUnits)) {
            $this->unit = $unit;
        }

        return $this;
    }

    /**
     * Returns the amount of minutes
     * from the start date to end date
     * counting only business hours
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return int
     * @throws \Exception
     */
    public function diff(Carbon $start, Carbon $end)
    {
        if (!$this->businessOpensAt || !$this->businessClosesAt) throw new \Exception('Business hours not set');

        $start = $this->adjustDate($start);
        $end = $this->adjustDate($end);

        $minutes = 0;

        $currentDay = $start->copy();
        $currentDayStart = $start->copy()->startOfDay();

        if ($start->isSameDay($end)) {
            $minutes = $start->diffInMinutes($end);

            return $minutes;
        }

        while ($currentDayStart < $end) {

            $currentDayBusinessStart = $this->businessStart($currentDayStart);
            $currentDayBusinessEnd = $this->businessEnd($currentDayStart);

            if ($end->isSameDay($currentDay)) {

                if ($end > $currentDayBusinessEnd) {
                    $minutes += $currentDayBusinessStart->diffInMinutes($currentDayBusinessEnd);
                }

                if ($end->between($currentDayBusinessStart, $currentDayBusinessEnd)) {
                    $minutes += $currentDay->diffInMinutes($end);
                }
                return $minutes;
            }

            if ($start->isSameDay($currentDay)) {
                $minutes += $currentDayBusinessEnd->diffInMinutes($start);
            } else {
                $minutes += $currentDayBusinessStart->diffInMinutes($currentDayBusinessEnd);
            }

            $currentDay->nextWeekday();
            $currentDayStart->nextWeekday();
        }

        return $minutes;
    }

    /**
     * Returns a date adjusted to a valid business time
     *
     * @param Carbon $date
     * @return Carbon
     */
    protected function adjustDate(Carbon $date)
    {
        $businessStart = $this->businessStart($date);
        $businessEnd = $this->businessEnd($date);

        if ($date < $businessStart) return $businessStart;

        if ($date > $businessEnd) return $businessEnd;

        return $date;
    }

    /**
     * Returns a copy of the Carbon object at the business start time
     *
     * @param Carbon $date
     * @return Carbon
     */
    protected function businessStart(Carbon $date)
    {
        return $date->copy()->startOfDay()->addHours($this->businessOpensAt);
    }

    /**
     * Returns a copy of the Carbon object at the business end time
     *
     * @param Carbon $date
     * @return Carbon
     */
    protected function businessEnd(Carbon $date)
    {
        return $date->copy()->startOfDay()->addHours($this->businessClosesAt);
    }
}
