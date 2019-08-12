<?php
declare(strict_types=1);

namespace Loyaltycorp\Schedule\ScheduleBundle\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;

interface ScheduleRunnerInterface
{
    /**
     * Run given schedule and display to given output.
     *
     * @param \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(ScheduleInterface $schedule, OutputInterface $output): void;
}
