<?php
declare(strict_types=1);

namespace Loyaltycorp\Schedule\ScheduleBundle;

use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface;
use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use Symfony\Component\Console\Application;

final class Schedule implements ScheduleInterface
{
    /** @var \Symfony\Component\Console\Application */
    private $app;

    /** @var \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface[] */
    private $events = [];

    /**
     * Schedule constructor.
     *
     * @param \Symfony\Component\Console\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->app->setAutoExit(false);
    }

    /**
     * Create event for given command and parameters.
     *
     * @param string $command
     * @param null|mixed[] $parameters
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface
     */
    public function command(string $command, ?array $parameters = null): EventInterface
    {
        $this->events[] = $event = new Event($command, $parameters);

        return $event;
    }

    /**
     * Get application the schedule belongs to.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Console\Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * Get due events.
     *
     * @return \Loyaltycorp\Schedule\ScheduleBundle\Interfaces\EventInterface[]
     */
    public function getDueEvents(): array
    {
        return \array_filter($this->events, static function (EventInterface $event): bool {
            return $event->isDue();
        });
    }
}
