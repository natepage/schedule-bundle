<?php
declare(strict_types=1);

namespace LoyaltyCorp\Schedule\ScheduleBundle\DependencyInjection\Compiler;

use LoyaltyCorp\Schedule\ScheduleBundle\Command\ScheduleRunCommand;
use Loyaltycorp\Schedule\ScheduleBundle\Interfaces\ScheduleProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SchedulePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ScheduleRunCommand::class);
        $definition->setArgument(0, $this->getProviderReferences($container));
    }

    /**
     * Get references for scheduled command providers.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \Symfony\Component\DependencyInjection\Reference[]
     */
    private function getProviderReferences(ContainerBuilder $container): array
    {
        $tagged = $container->findTaggedServiceIds(ScheduleProviderInterface::TAG_SCHEDULE_PROVIDER);
        $references = [];

        foreach (\array_keys($tagged) as $id) {
            $references[] = new Reference($id);
        }

        return $references;
    }
}
