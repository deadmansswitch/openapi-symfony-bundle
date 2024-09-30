<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper;

final class TypeMapper implements TypeMapperInterface
{
    /**
     * @throws TypeMapperException
     */
    public function getOpenApiType(string $phpType): string
    {
        if (enum_exists($phpType)) {
            return 'string';
        }

        if (class_exists($phpType)) {
            return 'object';
        }

        return match ($phpType) {
            'int'       => 'integer',
            'float'     => 'number',
            'string'    => 'string',
            'bool'      => 'boolean',
            'array'     => 'array',
            'object'    => 'object',
            'null'      => 'null',
            default     => throw new TypeMapperException('Unable to map PHP type to OpenAPI type: ' . $phpType),
        };
    }

    /**
     * @throws TypeMapperException
     */
    public function getPhpType(string $openApiType): string
    {
        return match ($openApiType) {
            'integer'   => 'int',
            'number'    => 'float',
            'string'    => 'string',
            'boolean'   => 'bool',
            'array'     => 'array',
            'object'    => 'object',
            'null'      => 'null',
            default     => throw new TypeMapperException('Unable to map OpenAPI type to PHP type: ' . $openApiType),
        };
    }
}