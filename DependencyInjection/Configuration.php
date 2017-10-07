<?php

namespace Deetrych\MappingBundle\DependencyInjection;

use Deetrych\Mapping\Mapper\ReadModel\ArrayMapper as ReadModelArrayMapper;
use Deetrych\Mapping\Mapper\ReadModel\JsonMapper as ReadModelJsonMapper;
use Deetrych\Mapping\Mapper\WriteModel\ArrayMapper;
use Deetrych\Mapping\Mapper\WriteModel\JsonMapper;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('deetrych_mapping');

        $rootNode
            ->children()
                ->arrayNode('type_map')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('write_models')
                            ->prototype('scalar')->end()
                            ->defaultValue(['array' => ArrayMapper::class, 'json' => JsonMapper::class])
                        ->end()
                        ->arrayNode('read_models')
                            ->prototype('scalar')->end()
                            ->defaultValue(['array' => ReadModelArrayMapper::class, 'json' => ReadModelJsonMapper::class])
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('write_models')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('fields')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('path')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('type')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('read_models')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('fields')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('path')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('type')->end()
                            ->scalarNode('model')->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;


        return $treeBuilder;
    }
}
