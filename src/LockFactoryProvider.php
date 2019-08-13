<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle;

use LoyaltyCorp\Schedule\ScheduleBundle\Interfaces\LockFactoryProviderInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

final class LockFactoryProvider implements LockFactoryProviderInterface
{
    /**
     * Get lock factory.
     *
     * @return \Symfony\Component\Lock\Factory
     */
    public function getFactory(): Factory
    {
        return new Factory(new FlockStore());
    }
}
