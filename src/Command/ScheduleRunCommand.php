<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Command;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduleRunCommand extends Command
{
    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface */
    private $schedule;

    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface */
    private $runner;

    /**
     * ScheduleRunCommand constructor.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleRunnerInterface $runner
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     */
    public function __construct(ScheduleRunnerInterface $runner, ScheduleInterface $schedule)
    {
        parent::__construct();

        $this->runner = $runner;
        $this->schedule = $schedule;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('schedule:run')
            ->setDescription('Run scheduled commands');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->schedule->setApplication($this->getApplication());

        $this->runner->run($this->schedule, $output);
    }
}
