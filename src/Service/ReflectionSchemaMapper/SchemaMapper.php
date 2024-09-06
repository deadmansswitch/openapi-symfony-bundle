<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionClassSchemaMapper;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Mapper\ReflectionPropertyWithBuiltinTypeSchemaMapper;
use LogicException;
use Reflector;

final class SchemaMapper
{
    private readonly array $mappers;

    public function __construct()
    {
        // TODO: Replace constructor with DI

        $this->mappers = [
            new ReflectionClassSchemaMapper($this),
            new ReflectionPropertyWithBuiltinTypeSchemaMapper(),
        ];
    }

    public function map(Reflector $ref): Schema
    {
        foreach ($this->mappers as $mapper) {
            assert($mapper instanceof SchemaMapperInterface);

            if ($mapper->supports($ref)) {
                return $mapper->map($ref);
            }
        }

        throw new LogicException('Unsupported reflection type');
    }
}