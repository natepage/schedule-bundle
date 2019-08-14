<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\DataCollector;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface;
use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\TraceableScheduleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class ScheduleDataCollector extends DataCollector
{
    /** @var string */
    public const NAME = 'schedule.schedule_collector';

    /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface */
    private $schedule;

    /**
     * ScheduleDataCollector constructor.
     *
     * @param \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\ScheduleInterface $schedule
     */
    public function __construct(ScheduleInterface $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        if (($this->schedule instanceof TraceableScheduleInterface) === false) {
            return;
        }

        /** @var \LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\TraceableScheduleInterface $schedule */
        $schedule = $this->schedule;

        $this->data = $schedule->getData();
    }

    /**
     * Get data.
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $this->data = [];
    }
}
