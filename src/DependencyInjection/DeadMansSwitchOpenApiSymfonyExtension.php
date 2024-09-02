<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Attribute\AsPropertyFormatGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser\EmailFormatGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserStrategy;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserStrategyInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DeadMansSwitchOpenApiSymfonyExtension extends Extension
{
    public const ALIAS  = 'dead_mans_switch_openapi';
    private const PREFIX = 'dead_mans_switch.openapi.symfony.property_format_guesser';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::ALIAS . ".config", $config);

        $this->registerPropertyTypeGuesserStrategy($container);
    }

    private function registerPropertyTypeGuesserStrategy(ContainerBuilder $container): void
    {
        $container
            ->registerAttributeForAutoconfiguration(
                attributeClass: AsPropertyFormatGuesser::class,
                configurator: static function(ChildDefinition $def, AsPropertyFormatGuesser $attr, ReflectionClass $ref): void
                {
                    $def->addTag(self::PREFIX . '.guesser');
                }
            );

        $container
            ->register(self::PREFIX . '.guessers.email', EmailFormatGuesser::class)
            ->addTag(self::PREFIX . '.guesser')
        ;

        $container
            ->register(id: self::PREFIX . '.strategy', class: GuesserStrategy::class)
            ->setArgument('$guessers', new TaggedIteratorArgument(tag: self::PREFIX . '.guesser'))
        ;

        $container
            ->setAlias(alias: GuesserStrategyInterface::class, id: self::PREFIX . '.strategy')
            ->setPublic(true)
        ;
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }
}