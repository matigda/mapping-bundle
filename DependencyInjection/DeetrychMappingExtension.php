<?php

namespace Deetrych\MappingBundle\DependencyInjection;

use Deetrych\Mapping\Decorator\FactoryDecorator as WriteModelFactory;
use Deetrych\Mapping\Mapper\ReadModel\Factory as ReadModelFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DeetrychMappingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->createMapperServices($container, $config, 'write_model');
        $this->createMapperServices($container, $config, 'read_model');

    }

    private function createMapperServices(ContainerBuilder $container, array $config, string $type)
    {
        $plural = $type . 's';

        $factoryDefinition = $this->createMapperFactoryDefinition($config, $type, $plural);

        $container->setDefinition('deetrych.factory.' . $type, $factoryDefinition);

        foreach (array_keys($config[$plural]) as $writeModelMapperName) {
            $mapperDefinition = new Definition(null);

            $mapperDefinition->setFactory(
                [
                    new Reference('deetrych.factory.' . $type),
                    'createFromType'
                ]
            );
            $mapperDefinition->addArgument($config[$plural][$writeModelMapperName]['type']);

            $container->setDefinition(
                sprintf('deetrych.%s.mapper.%s', $type, $writeModelMapperName),
                $mapperDefinition
            );
        }
    }

    private function createMapperFactoryDefinition(array $config, string $type, string $plural): Definition
    {
        $class = ReadModelFactory::class;

        $args = [
            $config[$plural],
            $config['type_map'][$plural]
        ];

        if ($type == 'write_model') {
            array_unshift($args, new Reference('deetrych.property_access_provider'));
            $class = WriteModelFactory::class;
        }

        return new Definition(
            $class,
            $args
        );
    }
}
