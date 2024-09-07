<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Reflector;

final class ReflectionPropertyWithCustomTypeMapper implements SchemaMapperConcreteInterface
{
    public function __construct(private readonly SchemaMapper $mapper) {}

    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionProperty) {
            return false;
        }

        if ($reflector->getType()->isBuiltin()) {
            return false;
        }

        return true;
    }

    /**
     * @throws ReflectionException
     */
    public function map(Reflector $reflector): Schema
    {
        assert($reflector instanceof ReflectionProperty);

        $type = $reflector->getType();
        assert($type instanceof ReflectionNamedType);

        $name = $type->getName();
        $ref = new ReflectionClass($name);

        return $this->mapper->map($ref);
    }
}