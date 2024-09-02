<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(DeadMansSwitchOpenApiSymfonyExtension::ALIAS);

        $builder->getRootNode()
            ->children()
                ->scalarNode('openapi')
                    ->defaultValue('3.0.0')
                ->end()
                ->arrayNode('info')
                    ->children()
                        ->scalarNode('title')
                            ->defaultValue('DeadMan\sSwitch OpenApi')
                        ->end()
                        ->scalarNode('version')
                            ->defaultValue('1.0.0')
                        ->end()
                        ->scalarNode('summary')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('termsOfService')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
