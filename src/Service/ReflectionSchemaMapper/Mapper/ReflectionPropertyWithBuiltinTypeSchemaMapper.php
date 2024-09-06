<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use ReflectionNamedType;
use ReflectionProperty;
use Reflector;

final class ReflectionPropertyWithBuiltinTypeSchemaMapper implements SchemaMapperInterface
{
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

        return new Schema(type: $type->getName());
    }
}