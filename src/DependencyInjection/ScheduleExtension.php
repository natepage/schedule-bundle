<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\DependencyInjection;

use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class ScheduleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container
            ->registerForAutoconfiguration(ScheduleProviderInterface::class)
            ->addTag(ScheduleProviderInterface::TAG_SCHEDULE_PROVIDER);
    }
}
