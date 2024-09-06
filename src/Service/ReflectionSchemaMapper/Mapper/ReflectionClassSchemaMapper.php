<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\SchemasMap;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use ReflectionClass;
use Reflector;

final class ReflectionClassSchemaMapper implements SchemaMapperInterface
{
    public function __construct(private readonly SchemaMapper $mapper) {}

    public function supports(Reflector $reflector): bool
    {
        return $reflector instanceof ReflectionClass;
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