<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Attribute\AsPropertyFormatGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser\EmailFormatGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserStrategy;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserStrategyInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionBackedEnumSchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionClassSchemaMapperConcrete;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionPropertyWithBuiltinTypeSchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionPropertyWithCustomTypeMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor\PathEntityIdentifierExtractor;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\ExtractorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\RequestParametersExtractor;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\RequestParametersExtractorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapperInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class DeadMansSwitchOpenApiSymfonyExtension extends Extension
{
    public const ALIAS  = 'dead_mans_switch_openapi';
    private const PREFIX = 'dead_mans_switch.openapi.symfony.property_format_guesser';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::ALIAS . ".config", $config);

        $this->registerRequestParametersExtractor($container);
        $this->registerReflectionSchemaMapper($container);
        $this->registerPropertyTypeGuesserStrategy($container);
        $this->registerTypeMapper($container);
    }

    private function registerRequestParametersExtractor(ContainerBuilder $container): void
    {
        $container
            ->register(id: self::PREFIX . '.request_parameters_extractor.query_entity_identifier', class: PathEntityIdentifierExtractor::class)
            ->addTag(name: ExtractorInterface::TAG)
        ;

        $container
            ->register(id: self::ALIAS . '.request_parameters_extractor', class: RequestParametersExtractor::class)
            ->setArgument('$extractors', new TaggedIteratorArgument(tag: ExtractorInterface::TAG))
        ;

        $container
            ->setAlias(alias: RequestParametersExtractorInterface::class, id: self::ALIAS . '.request_parameters_extractor')
            ->setPublic(true)
        ;
    }

    private function registerReflectionSchemaMapper(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(SchemaMapperConcreteInterface::class)
            ->addTag(name: SchemaMapperConcreteInterface::TAG)
        ;

        // TODO: try to remove `setTags` from concrete mappers and check does autoconfiguration works

        $container
            ->register(id: self::ALIAS . '.mapper.backed_enum', class: ReflectionBackedEnumSchemaMapper::class)
            ->addTag(SchemaMapperConcreteInterface::TAG)
        ;

        // TODO: implement collection output dto schema mapper and register it here, after backed enum mapper

        $container
            ->register(id: self::ALIAS . '.mapper.class', class: ReflectionClassSchemaMapperConcrete::class)
            ->setArgument('$mapper', new Reference(self::ALIAS . '.mapper.strategy'))
            ->addTag(SchemaMapperConcreteInterface::TAG)
        ;

        $container
            ->register(id: self::ALIAS . '.mapper.property_of_builtin_type', class: ReflectionPropertyWithBuiltinTypeSchemaMapper::class)
            ->setArgument('$typeMapper', new Reference(TypeMapperInterface::class))
            ->addTag(SchemaMapperConcreteInterface::TAG)
        ;

        $container
            ->register(id: self::ALIAS . '.mapper.property_of_non_builtin_type', class: ReflectionPropertyWithCustomTypeMapper::class)
            ->setArgument('$mapper', new Reference(self::ALIAS . '.mapper.strategy'))
            ->addTag(SchemaMapperConcreteInterface::TAG)
        ;

        $container
            ->register(id: self::ALIAS . '.mapper.strategy', class: SchemaMapper::class)
            ->setArgument('$mappers', new TaggedIteratorArgument(tag: SchemaMapperConcreteInterface::TAG))
        ;

        $container
            ->setAlias(alias: SchemaMapperInterface::class, id: self::ALIAS . '.mapper.strategy')
            ->setPublic(true)
        ;
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

    private function registerTypeMapper(ContainerBuilder $container): void
    {
        $container->register(id: self::PREFIX . '.type_mapper', class: TypeMapper::class);
        $container->setAlias(alias: TypeMapperInterface::class, id: self::PREFIX . '.type_mapper')->setPublic(true);
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }
}