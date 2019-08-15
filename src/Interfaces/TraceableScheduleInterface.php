<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

interface TraceableScheduleInterface extends ScheduleInterface
{
    /**
     * Get events indexed by their profiler class.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface[]
     */
    public function getEvents(): array;

    /**
     * Get providers.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[]
     */
    public function getProviders(): array;
}
