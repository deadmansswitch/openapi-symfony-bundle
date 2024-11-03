<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapperInterface;
use ReflectionParameter;
use Reflector;
use RuntimeException;

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
            enum: $this->isBackedEnum($type) ? $this->getEnumValues($type) : null,
        );
    }

    private function isBackedEnum(string $type): bool
    {
        return enum_exists($type);
    }

    private function getEnumValues(string $enum): array
    {
        if (!enum_exists($enum)) {
            throw new RuntimeException();
        }

        return array_map(
            callback: static fn ($case): string => $case->value,
            array: $enum::cases(),
        );
    }
}