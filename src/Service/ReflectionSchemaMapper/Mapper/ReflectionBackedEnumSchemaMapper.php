<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use BackedEnum;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use ReflectionClass;
use Reflector;

final class ReflectionBackedEnumSchemaMapper implements SchemaMapperConcreteInterface
{
    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionClass) {
            return false;
        }

        if (!$reflector->implementsInterface(BackedEnum::class)) {
            return false;
        }

        return true;
    }

    public function map(Reflector $reflector): Schema
    {
        assert($reflector instanceof ReflectionClass);
        assert($reflector->implementsInterface(BackedEnum::class));

        $cases = $reflector->getMethod('cases')->invoke(null);

        $values = [];
        foreach ($cases as $case) {
            assert($case instanceof BackedEnum);
            $values[] = $case->value;
        }

        return new Schema(
            type: 'string',
            format: 'enum',
            example: $values[0] ?? null,
            enum: !empty($values) ? $values : null,
        );
    }
}