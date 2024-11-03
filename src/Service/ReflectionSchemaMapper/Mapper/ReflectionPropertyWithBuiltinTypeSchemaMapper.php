<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapperInterface;
use ReflectionNamedType;
use ReflectionProperty;
use Reflector;

final class ReflectionPropertyWithBuiltinTypeSchemaMapper implements SchemaMapperConcreteInterface
{
    public function __construct(private readonly TypeMapperInterface $typeMapper) {}

    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionProperty) {
            return false;
        }

        if (!$reflector->getType() instanceof ReflectionNamedType) {
            return false;
        }

        return $reflector->getType()->isBuiltin();
    }

    public function map(Reflector $reflector): Schema
    {
        assert($reflector instanceof ReflectionProperty);

        $type = $reflector->getType();
        assert($type instanceof ReflectionNamedType);

        return new Schema(type: $this->typeMapper->getOpenApiType($type->getName()));
    }
}