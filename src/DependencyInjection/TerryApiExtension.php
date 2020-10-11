<?php

declare(strict_types=1);

namespace TerryApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class TerryApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('service.xml');

        /** @var array<string, array<string, array<string,mixed>>> $processedConfigs */
        $processedConfigs = $this->processConfiguration(new Configuration(), $configs);

        $container->getDefinition('terry_api.negotiation.content_negotiator')->replaceArgument(0, $processedConfigs['serialize']['formats']);
        $container->getDefinition('terry_api.negotiation.content_negotiator')->replaceArgument(1, $processedConfigs['serialize']['format_default']);
        $container->getDefinition('terry_api.serialize.format_mapper')->replaceArgument(0, $processedConfigs['serialize']['formats']);
    }
}
