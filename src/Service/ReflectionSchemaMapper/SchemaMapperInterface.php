<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use Reflector;

interface SchemaMapperInterface
{
    public function supports(Reflector $reflector): bool;

    public function map(Reflector $reflector): Schema;
}