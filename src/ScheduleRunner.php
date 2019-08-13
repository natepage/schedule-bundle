<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;

final class ScheduleRunner implements ScheduleRunnerInterface
{
    /** @var \Symfony\Component\Lock\Factory */
    private $lockFactory;

    /** @var bool */
    private $ran = false;

    /**
     * Run given schedule and display to given output.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
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

            $description = $event->getDescription();
            $lock = $this->lockFactory->createLock($event->getLockResource());

            $output->writeln(\sprintf('<info>Running scheduled command:</info> %s', $description));

            if ($event->allowsOverlapping() === false && $lock->acquire() === false) {
                $output->writeln(\sprintf('Abort execution of "%s" to prevent overlapping', $description));

                continue;
            }

            $event->run($schedule->getApplication());

            $this->ran = true;
        }

        if ($this->ran === false) {
            $output->writeln('<info>No scheduled commands are ready to run.</info>');
        }
    }

    /**
     * Set lock factory.
     *
     * @param \Symfony\Component\Lock\Factory $factory
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface
     */
    public function setLockFactory(Factory $factory): ScheduleRunnerInterface
    {
        $this->lockFactory = $factory;

        return $this;
    }
}
