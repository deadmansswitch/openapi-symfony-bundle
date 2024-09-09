<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapperInterface;
use ReflectionParameter;
use Reflector;

final class ReflectionParameterSchemaMapper implements SchemaMapperConcreteInterface
{
    public function __construct(private readonly TypeMapperInterface $typeMapper) {}

    public function supports(Reflector $reflector): bool
    {
        return $reflector instanceof ReflectionParameter;
    }

    public function map(Reflector $reflector): Schema
    {
        assert($reflector instanceof ReflectionParameter);

        $type = $reflector->getType()->getName();

        return new Schema(
            type: $this->typeMapper->getOpenApiType($type),
        );
    }
}