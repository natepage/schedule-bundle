<?php
declare(strict_types=1);

namespace Loyaltycorp\Schedule\ScheduleBundle\Interfaces;

interface ScheduleProviderInterface
{
    /** @var string */
    public const TAG_SCHEDULE_PROVIDER = 'schedule.provider';

    /**
     * Schedule command on given schedule.
     *
     * @param \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     *
     * @return void
     */
    public function schedule(ScheduleInterface $schedule): void;
}
