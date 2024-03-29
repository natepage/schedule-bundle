<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;

interface ScheduleRunnerInterface
{
    /**
     * Run given schedule and display to given output.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(ScheduleInterface $schedule, OutputInterface $output): void;

    /**
     * Set lock factory provider.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\LockFactoryProviderInterface $provider
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface
     */
    public function setLockFactoryProvider(LockFactoryProviderInterface $provider): self;
}
