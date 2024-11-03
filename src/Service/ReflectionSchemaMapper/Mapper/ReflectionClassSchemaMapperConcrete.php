<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use BackedEnum;
use DeadMansSwitch\OpenApi\Schema\V3_0\Extra\SchemasMap;
use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use ReflectionClass;
use Reflector;

final class ReflectionClassSchemaMapperConcrete implements SchemaMapperConcreteInterface
{
    public function __construct(private readonly SchemaMapper $mapper) {}

    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionClass) {
            return false;
        }

        if ($reflector->implementsInterface(BackedEnum::class)) {
            return false;
        }

        return true;
    }

    public function map(Reflector $reflector): Schema
    {
        assert($reflector instanceof ReflectionClass);

        $properties = [];

        foreach ($reflector->getProperties() as $property) {
            $properties[$property->getName()] = $this->mapper->map($property);
        }

        return new Schema(
            type: 'object',
            properties: SchemasMap::fromArray($properties),
        );
    }
}