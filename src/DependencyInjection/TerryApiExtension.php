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
        $loader->load('factory.xml');
        $loader->load('annotation.xml');
        $loader->load('listener.xml');
        $loader->load('resolver.xml');

        $processedConfigs = $this->processConfiguration(new Configuration(), $configs);

        $container->getDefinition('terry_api.factory.http_server_factory')->replaceArgument(0, $processedConfigs['http_server'] ?? []);

        if (false === $processedConfigs['event_listener']['http_error_listener']['enable']) {
            $container->removeDefinition('terry_api.event_listener.http_error_listener');
        }

        if (false === $processedConfigs['event_listener']['response_transform_listener']['enable']) {
            $container->removeDefinition('terry_api.event_listener.response_transform_listener');
        }

        if (false === $processedConfigs['argument_value_resolver']['abstract_http_client_resolver']['enable']) {
            $container->removeDefinition('terry_api.argument_value_resolver.abstract_http_client_resolver');
        }

        if (false === $processedConfigs['argument_value_resolver']['request_single_struct_resolver']['enable']) {
            $container->removeDefinition('terry_api.argument_value_resolver.request_single_struct_resolver');
        }

        if (false === $processedConfigs['argument_value_resolver']['request_array_of_structs_resolver']['enable']) {
            $container->removeDefinition('terry_api.argument_value_resolver.request_array_of_structs_resolver');
        }
    }
}
