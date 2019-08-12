<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

use Symfony\Component\Console\Application;

interface ScheduleInterface
{
    /**
     * Create event for given command and parameters.
     *
     * @param string $command
     * @param null|mixed[] $parameters
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function command(string $command, ?array $parameters = null): EventInterface;

    /**
     * Get application the schedule belongs to.
     *
     * @return \Symfony\Component\Console\Application
     */
    public function getApp(): Application;

    /**
     * Get due events.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface[]
     */
    public function getDueEvents(): array;
}
