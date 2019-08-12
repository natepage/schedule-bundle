<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

interface ScheduleProviderInterface
{
    /** @var string */
    public const TAG_SCHEDULE_PROVIDER = 'schedule.provider';

    /**
     * Schedule command on given schedule.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     *
     * @return void
     */
    public function schedule(ScheduleInterface $schedule): void;
}
