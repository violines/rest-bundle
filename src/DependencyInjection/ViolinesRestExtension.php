<?php

declare(strict_types=1);

namespace Violines\RestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
final class ViolinesRestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('service.xml');

        /** @var array<string, array<string, array<string,mixed>>> $processedConfigs */
        $processedConfigs = $this->processConfiguration(new Configuration(), $configs);

        $container->getDefinition('violines_rest.negotiation.content_negotiator')->replaceArgument(0, $processedConfigs['serialize']['formats']);
        $container->getDefinition('violines_rest.negotiation.content_negotiator')->replaceArgument(1, $processedConfigs['serialize']['format_default']);
        $container->getDefinition('violines_rest.serialize.format_mapper')->replaceArgument(0, $processedConfigs['serialize']['formats']);
    }
}
