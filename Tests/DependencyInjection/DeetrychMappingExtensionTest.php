<?php

declare(strict_types = 1);

namespace Deetrych\MappingBundle\Tests\DependencyInjection;

use Deetrych\Mapping\Mapper\ReadModel\ArrayMapper as ReadModelArrayMapper;
use Deetrych\Mapping\Mapper\WriteModel\ArrayMapper as WriteModelArrayMapper;
use Deetrych\MappingBundle\DependencyInjection\DeetrychMappingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class DeetrychMappingExtensionTest extends TestCase
{
    public function testExtensionIsProperlyLoadingServices()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.debug', true);

        $extension = new DeetrychMappingExtension();
        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('deetrych.write_model.mapper.some_name'));
        $this->assertInstanceOf(
            WriteModelArrayMapper::class,
            $containerBuilder->get('deetrych.write_model.mapper.some_name')
        );

        $this->assertTrue($containerBuilder->hasDefinition('deetrych.write_model.mapper.other_name'));
        $this->assertInstanceOf(
            WriteModelArrayMapper::class,
            $containerBuilder->get('deetrych.write_model.mapper.other_name')
        );

        $this->assertTrue($containerBuilder->hasDefinition('deetrych.read_model.mapper.other_name'));
        $this->assertInstanceOf(
            ReadModelArrayMapper::class,
            $containerBuilder->get('deetrych.read_model.mapper.other_name')
        );
    }
}
