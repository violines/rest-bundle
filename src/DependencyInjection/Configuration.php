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
                        // array_response_listener
                        ->arrayNode('array_response_listener')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // http_error_listener
                        ->arrayNode('http_error_listener')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // object_response_listener
                        ->arrayNode('object_response_listener')
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
                        // request_single_struct_resolver
                        ->arrayNode('single_object_resolver')
                        ->addDefaultsIfNotSet()
                            ->children()
                            ->booleanNode('enable')->defaultTrue()->end()
                            ->end()
                        ->end()
                        // request_array_of_structs_resolver
                        ->arrayNode('objects_array_resolver')
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
                        ->addDefaultsIfNotSet()
                            ->children()
                                // json
                                ->arrayNode('json')
                                    ->scalarPrototype()
                                    ->end()
                                ->end()
                                // xml
                                ->arrayNode('xml')
                                    ->scalarPrototype()
                                    ->end()
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
