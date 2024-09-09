<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Reference;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Exception\ClassNotExistsException;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperConcreteInterface;
use DeadMansSwitch\OpenApi\Symfony\Util\Namer;
use ReflectionClass;
use Reflector;
use Traversable;

final class ReflectionTypedCollectionSchemaMapper implements SchemaMapperConcreteInterface
{
    public function supports(Reflector $reflector): bool
    {
        if (!$reflector instanceof ReflectionClass) {
            return false;
        }

        if (!$reflector->implementsInterface(Traversable::class)) {
            return false;
        }

        if (!$reflector->hasMethod('getItemClassName')) {
            return false;
        }

        return true;
    }

    /**
     * @throws ClassNotExistsException
     */
    public function map(Reflector $reflector): Schema
    {
        $class = $reflector->getMethod('getItemClassName')->invoke(null);
        if (!class_exists($class)) {
            throw new ClassNotExistsException("Typed Collection declared item class name of non-existing class: " . $class);
        }

        $name = Namer::schema($class);

        return new Schema(
            type: 'array',
            items: new Reference(ref: $name),
        );
    }
}