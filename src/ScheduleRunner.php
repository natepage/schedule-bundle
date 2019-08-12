<?php
declare(strict_types=1);

namespace Loyaltycorp\Schedule\ScheduleBundle;

use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduleRunner implements ScheduleRunnerInterface
{
    /** @var bool */
    private $ran = false;

    /**
     * Run given schedule and display to given output.
     *
     * @param \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(ScheduleInterface $schedule, OutputInterface $output): void
    {
        foreach ($schedule->getDueEvents() as $event) {
            if ($event->filtersPass() === false) {
                continue;
            }

            $output->writeln(\sprintf('<info>Running scheduled command:</info> %s', $event->getDescription()));

            $event->run($schedule->getApp());

            $this->ran = true;
        }

        if ($this->ran === false) {
            $output->writeln('<info>No scheduled commands are ready to run.</info>');
        }
    }
}
