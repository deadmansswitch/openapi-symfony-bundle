<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use Reflector;

interface SchemaMapperConcreteInterface
{
    public const TAG = 'dead_mans_switch.openapi.symfony.mapper';

    public function supports(Reflector $reflector): bool;

    public function map(Reflector $reflector): Schema;
}