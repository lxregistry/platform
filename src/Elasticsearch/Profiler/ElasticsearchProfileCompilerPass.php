<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\Profiler;

use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ElasticsearchProfileCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $esEnabled = (int) $_SERVER['SHOPWARE_ES_ENABLED'];
        if (!$container->getParameter('kernel.debug') || $esEnabled === 0) {
            $container->removeDefinition(DataCollector::class);

            return;
        }

        $clientDecorator = new Definition(ClientProfiler::class);
        $clientDecorator->setArguments([
            new Reference('shopware.es.profiled.client.inner'),
        ]);
        $clientDecorator->setDecoratedService(Client::class);

        $container->setDefinition('shopware.es.profiled.client', $clientDecorator);
    }
}
