<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\TraceableScheduleInterface;
use Symfony\Component\Console\Application;

final class TraceableSchedule implements TraceableScheduleInterface
{
    /** @var string */
    private $currentProvider;

    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface */
    private $decorated;

    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface[] */
    private $events = [];

    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[] */
    private $providers = [];

    /**
     * TraceableSchedule constructor.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $decorated
     */
    public function __construct(ScheduleInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Add schedule providers.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[] $providers
     *
     * @return self
     */
    public function addProviders(array $providers): ScheduleInterface
    {
        foreach ($providers as $provider) {
            $this->currentProvider = \get_class($provider);
            $this->providers[] = $provider;

            $provider->schedule($this);
        }

        return $this;
    }

    /**
     * Create event for given command and parameters.
     *
     * @param string $command
     * @param null|mixed[] $parameters
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function command(string $command, ?array $parameters = null): EventInterface
    {
        $event = $this->decorated->command($command, $parameters);

        if (isset($this->events[$this->currentProvider]) === false) {
            $this->events[$this->currentProvider] = [];
        }

        $this->events[$this->currentProvider][] = $event;

        return $event;
    }

    /**
     * Get application the schedule belongs to.
     *
     * @return \Symfony\Component\Console\Application
     */
    public function getApplication(): Application
    {
        return $this->decorated->getApplication();
    }

    /**
     * Get due events.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface[]
     */
    public function getDueEvents(): array
    {
        return $this->decorated->getDueEvents();
    }

    /**
     * Get events indexed by their profiler class.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\EventInterface[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Get providers.
     *
     * @return \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Set application.
     *
     * @param \Symfony\Component\Console\Application $app
     *
     * @return self
     */
    public function setApplication(Application $app): ScheduleInterface
    {
        $this->decorated->setApplication($app);

        return $this;
    }
}
