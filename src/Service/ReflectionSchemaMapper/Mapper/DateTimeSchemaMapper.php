<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DateTimeInterface;
use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use ReflectionClass;
use ReflectionProperty;
use Reflector;

final class DateTimeSchemaMapper implements SchemaMapperConcreteInterface
{
    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionProperty) {
            return false;
        }

        $type = $reflector->getType();
        if ($type === null) {
            return false;
        }

        if ($type->getName() === DateTimeInterface::class) {
            return true;
        }

        $class = $type->getName();
        if (!class_exists($class)) {
            return false;
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->implementsInterface(DateTimeInterface::class)) {
            return false;
        }

        return true;
    }

    public function map(Reflector $reflector): Schema
    {
        return new Schema(
            type: 'string',
            format: 'date-time',
            example: '2017-07-21T17:32:28Z',
        );
    }
}