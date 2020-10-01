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

        $container->getDefinition('terry_api.http_client.server_settings_factory')->replaceArgument(0, $processedConfigs['http_server'] ?? []);

        if (false === $processedConfigs['event_listener']['array_response_listener']['enable']) {
            $container->removeDefinition('terry_api.event_listener.array_response_listener');
        }

        if (false === $processedConfigs['event_listener']['http_error_listener']['enable']) {
            $container->removeDefinition('terry_api.event_listener.http_error_listener');
        }

        if (false === $processedConfigs['event_listener']['object_response_listener']['enable']) {
            $container->removeDefinition('terry_api.event_listener.object_response_listener');
        }

        if (false === $processedConfigs['argument_value_resolver']['single_object_resolver']['enable']) {
            $container->removeDefinition('terry_api.argument_value_resolver.single_object_resolver');
        }

        if (false === $processedConfigs['argument_value_resolver']['objects_array_resolver']['enable']) {
            $container->removeDefinition('terry_api.argument_value_resolver.objects_array_resolver');
        }
    }
}
