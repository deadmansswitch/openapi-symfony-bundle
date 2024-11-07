<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use ReflectionClass;
use Reflector;
use Symfony\Component\Uid\Uuid;

final class ReflectionUuidSchemaMapper implements SchemaMapperConcreteInterface
{
    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionClass) {
            return false;
        }

        if ($reflector->getName() !== Uuid::class) {
            return false;
        }

        return true;
    }

    public function map(Reflector $reflector): Schema
    {
        return new Schema(
            type: 'string',
            format: 'uuid',
            example: '580becee-83bc-4a62-a368-e2928a45c732',
        );
    }
}