<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

interface TraceableScheduleInterface extends ScheduleInterface
{
    /**
     * Get profiler data.
     *
     * @return mixed[]
     */
    public function getData(): array;
}
