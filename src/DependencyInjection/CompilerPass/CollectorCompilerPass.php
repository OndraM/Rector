<?php declare(strict_types=1);

namespace Rector\DependencyInjection\CompilerPass;

use Rector\Contract\Rector\RectorInterface;
use Rector\NodeTraverser\RectorNodeTraverser;
use Rector\Rector\RectorCollector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;

final class CollectorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->collectCommandsToConsoleApplication($containerBuilder);
        $this->collectRectorsToMainNodeTraverser($containerBuilder);
        $this->collectRectorsToRectorCollector($containerBuilder);
    }

    private function collectCommandsToConsoleApplication(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }

    private function collectRectorsToMainNodeTraverser(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            RectorNodeTraverser::class,
            RectorInterface::class,
            'addVisitor'
        );
    }

    private function collectRectorsToRectorCollector(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            RectorCollector::class,
            RectorInterface::class,
            'addRector'
        );
    }
}
