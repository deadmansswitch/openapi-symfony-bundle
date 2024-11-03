<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper;

use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use LogicException;
use Reflector;
use Traversable;

final class SchemaMapper implements SchemaMapperInterface
{
    public function __construct(private readonly Traversable $mappers) {}

    public function map(Reflector $reflector): Schema
    {
        foreach ($this->mappers as $mapper) {
            assert($mapper instanceof SchemaMapperConcreteInterface);

            if ($mapper->supports($reflector)) {
                return $mapper->map($reflector);
            }
        }

        throw new LogicException('Unsupported reflection type');
    }
}