<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Command;

use LoyaltyCorp\Schedule\ScheduleBundle\Schedule;
use LoyaltyCorp\Schedule\ScheduleBundle\ScheduleRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduleRunCommand extends Command
{
    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[] */
    private $commandProviders;

    /**
     * ScheduleRunCommand constructor.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[] $commandProviders
     * @param string|null $name
     */
    public function __construct(array $commandProviders, ?string $name = null)
    {
        parent::__construct($name);

        $this->commandProviders = $commandProviders;
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
        $schedule = new Schedule($this->getApplication());

        foreach ($this->commandProviders as $provider) {
            $provider->schedule($schedule);
        }

        (new ScheduleRunner())->run($schedule, $output);
    }
}
