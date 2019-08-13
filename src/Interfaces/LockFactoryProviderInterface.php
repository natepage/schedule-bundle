<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\Interfaces;

use Symfony\Component\Lock\Factory;

interface LockFactoryProviderInterface
{
    /**
     * Get lock factory.
     *
     * @return \Symfony\Component\Lock\Factory
     */
    public function getFactory(): Factory;
}
