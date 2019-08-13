<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\LockFactoryProviderInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduleRunner implements ScheduleRunnerInterface
{
    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\LockFactoryProviderInterface */
    private $lockFactoryProvider;

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
            $lockFactory = $this->lockFactoryProvider->getFactory();
            $lock = $lockFactory->createLock($event->getLockResource(), $event->getMaxLockTime());

            $output->writeln(\sprintf('<info>Running scheduled command:</info> %s', $description));

            if ($event->allowsOverlapping() === false && $lock->acquire() === false) {
                $output->writeln(\sprintf('Abort execution of "%s" to prevent overlapping', $description));

                continue;
            }

            try {
                $event->run($schedule->getApplication());

                $lock->release();
            } finally {
                $lock->release();
            }

            $this->ran = true;
        }

        if ($this->ran === false) {
            $output->writeln('<info>No scheduled commands are ready to run.</info>');
        }
    }

    /**
     * Set lock factory provider.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\LockFactoryProviderInterface $provider
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface
     */
    public function setLockFactoryProvider(LockFactoryProviderInterface $provider): ScheduleRunnerInterface
    {
        $this->lockFactoryProvider = $provider;

        return $this;
    }
}
