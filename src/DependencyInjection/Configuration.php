<?php

declare(strict_types=1);

namespace TerryApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('terry_api');

        $treeBuilder->getRootNode()
            ->children()
                // event_listener
                ->arrayNode('event_listener')
                ->addDefaultsIfNotSet()
                    ->children()
                        // http_error_listener
                        ->arrayNode('http_error_listener')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // response_transform_listener
                        ->arrayNode('response_transform_listener')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // argument_value_resolver
                ->arrayNode('argument_value_resolver')
                ->addDefaultsIfNotSet()
                    ->children()
                        // abstract_client_resolver
                        ->arrayNode('abstract_client_resolver')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // request_single_struct_resolver
                        ->arrayNode('request_single_struct_resolver')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // request_array_of_structs_resolver
                        ->arrayNode('request_array_of_structs_resolver')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // http_server
                ->arrayNode('http_server')
                ->addDefaultsIfNotSet()
                    ->children()
                        // formats
                        ->arrayNode('formats')
                            ->children()
                                // json
                                ->arrayNode('json')
                                    ->scalarPrototype()->end()
                                ->end()
                                // xml
                                ->arrayNode('xml')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('format_default')->defaultNull()
                        ->end()
                    ->end()
                ->end();
                
            return $treeBuilder;
    }
}
