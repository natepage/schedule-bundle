<?php
declare(strict_types=1);

namespace Loyaltycorp\Schedule\ScheduleBundle;

use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface;
use Carbon\Carbon;
use Cron\CronExpression;

abstract class AbstractEvent implements EventInterface
{
    /** @var string */
    private $expression = '* * * * *';

    /** @var callable[] */
    private $filters = [];

    /** @var callable[] */
    private $rejects = [];

    /** @var \DateTimeZone|string */
    private $timezone;

    /**
     * Schedule the command at a given time.
     *
     * @param string $time
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function at(string $time): EventInterface
    {
        return $this->dailyAt($time);
    }

    /**
     * Schedule the event to run between start and end time.
     *
     * @param string $startTime
     * @param string $endTime
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function between(string $startTime, string $endTime): EventInterface
    {
        return $this->when($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * The Cron expression representing the event's frequency.
     *
     * @param string $expression
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function cron(string $expression): EventInterface
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Schedule the event to run daily.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function daily(): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0);
    }

    /**
     * Schedule the event to run daily at a given time (10:00, 19:30, etc).
     *
     * @param string $time
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function dailyAt(string $time): EventInterface
    {
        $segments = \explode(':', $time);

        return $this
            ->spliceIntoPosition(2, (int)$segments[0])
            ->spliceIntoPosition(1, \count($segments) === 2 ? (int)$segments[1] : '0');
    }

    /**
     * Set the days of the week the command should run on.
     *
     * @param mixed[]|mixed $days
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function days($days): EventInterface
    {
        $days = \is_array($days) ? $days : \func_get_args();

        return $this->spliceIntoPosition(5, \implode(',', $days));
    }

    /**
     * Schedule the event to run every fifteen minutes.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function everyFifteenMinutes(): EventInterface
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    /**
     * Schedule the event to run every five minutes.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function everyFiveMinutes(): EventInterface
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    /**
     * Schedule the event to run every minute.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function everyMinute(): EventInterface
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * Schedule the event to run every ten minutes.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function everyTenMinutes(): EventInterface
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    /**
     * Schedule the event to run every thirty minutes.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function everyThirtyMinutes(): EventInterface
    {
        return $this->spliceIntoPosition(1, '0,30');
    }

    /**
     * Check if the filters pass for the event.
     *
     * @return bool
     */
    public function filtersPass(): bool
    {
        foreach ($this->filters as $filter) {
            if ((bool)\call_user_func($filter) === false) {
                return false;
            }
        }

        foreach ($this->rejects as $reject) {
            if ((bool)\call_user_func($reject) === true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Schedule the event to run only on Fridays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function fridays(): EventInterface
    {
        return $this->days(5);
    }

    /**
     * Schedule the event to run hourly.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function hourly(): EventInterface
    {
        return $this->spliceIntoPosition(1, 0);
    }

    /**
     * Schedule the event to run hourly at a given offset in the hour.
     *
     * @param int $offset
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function hourlyAt(int $offset): EventInterface
    {
        return $this->spliceIntoPosition(1, $offset);
    }

    /**
     * Schedule the event to run only on Mondays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function mondays(): EventInterface
    {
        return $this->days(1);
    }

    /**
     * Schedule the event to run monthly.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function monthly(): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1);
    }

    /**
     * Schedule the event to run monthly on a given day and time.
     *
     * @param null|int $day
     * @param null|string $time
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function monthlyOn(?int $day = null, ?string $time = null): EventInterface
    {
        $this->dailyAt($time ?? '0:0');

        return $this->spliceIntoPosition(3, $day ?? 1);
    }

    /**
     * Schedule the event to run quarterly.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function quarterly(): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * Schedule the event to run only on Saturdays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function saturdays(): EventInterface
    {
        return $this->days(6);
    }

    /**
     * Register a callback to further filter the schedule.
     *
     * @param callable|bool $callback
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function skip($callback): EventInterface
    {
        $this->rejects[] = \is_callable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    /**
     * Splice the given value into the given position of the cron expression.
     *
     * @param int $position
     * @param int|string $value
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function spliceIntoPosition(int $position, $value): EventInterface
    {
        $segments = \explode(' ', $this->expression);

        $segments[$position - 1] = $value;

        return $this->cron(\implode(' ', $segments));
    }

    /**
     * Schedule the event to run only on Sundays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function sundays(): EventInterface
    {
        return $this->days(0);
    }

    /**
     * Schedule the event to run only on Thursdays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function thursdays(): EventInterface
    {
        return $this->days(4);
    }

    /**
     * Set the timezone the date should be evaluated on.
     *
     * @param \DateTimeZone|string $timezone
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function timezone($timezone): EventInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Schedule the event to run only on Tuesdays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function tuesdays(): EventInterface
    {
        return $this->days(2);
    }

    /**
     * Schedule the event to run twice daily.
     *
     * @param null|int $first
     * @param null|int $second
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function twiceDaily(?int $first = null, ?int $second = null): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, \sprintf('%d,%d', $first ?? 1, $second ?? 13));
    }

    /**
     * Schedule the event to run twice monthly.
     *
     * @param null|int $first
     * @param null|int $second
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function twiceMonthly(?int $first = null, ?int $second = null): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, \sprintf('%d,%d', $first ?? 1, $second ?? 16));
    }

    /**
     * Schedule the event to not run between start and end time.
     *
     * @param string $startTime
     * @param string $endTime
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function unlessBetween(string $startTime, string $endTime): EventInterface
    {
        return $this->skip($this->inTimeInterval($startTime, $endTime));
    }

    /**
     * Schedule the event to run only on Wednesdays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function wednesdays(): EventInterface
    {
        return $this->days(3);
    }

    /**
     * Schedule the event to run only on weekdays.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function weekdays(): EventInterface
    {
        return $this->spliceIntoPosition(5, '1-5');
    }

    /**
     * Schedule the event to run only on weekends.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function weekends(): EventInterface
    {
        return $this->spliceIntoPosition(5, '0,6');
    }

    /**
     * Schedule the event to run weekly.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function weekly(): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(5, 0);
    }

    /**
     * Schedule the event to run weekly on a given day and time.
     *
     * @param int $day
     * @param null|string $time
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function weeklyOn(int $day, ?string $time = null): EventInterface
    {
        $this->dailyAt($time ?? '0:0');

        return $this->spliceIntoPosition(5, $day);
    }

    /**
     * Register a callback to further filter the schedule.
     *
     * @param callable|bool $callback
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function when($callback): EventInterface
    {
        $this->filters[] = \is_callable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }

    /**
     * Schedule the event to run yearly.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function yearly(): EventInterface
    {
        return $this
            ->spliceIntoPosition(1, 0)
            ->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 1)
            ->spliceIntoPosition(4, 1);
    }

    /**
     * Determine if the Cron expression passes.
     *
     * @return bool
     */
    protected function expressionPasses(): bool
    {
        $date = Carbon::now();

        if ($this->timezone) {
            $date->setTimezone($this->timezone);
        }

        return CronExpression::factory($this->expression)->isDue($date->toDateTimeString());
    }

    /**
     * Get expression.
     *
     * @return string
     */
    protected function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Schedule the event to run between start and end time.
     *
     * @param string $startTime
     * @param string $endTime
     *
     * @return \Closure
     */
    private function inTimeInterval(string $startTime, string $endTime)
    {
        return function () use ($startTime, $endTime) {
            return Carbon::now($this->timezone)->between(
                Carbon::parse($startTime, $this->timezone),
                Carbon::parse($endTime, $this->timezone),
                true
            );
        };
    }
}
