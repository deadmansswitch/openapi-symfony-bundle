<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Util;

use function Symfony\Component\String\u;

final class Namer
{
    public static function schema(string $classname): string
    {
        return "#/components/schemas/" . self::schemaNameFromClassName($classname);
    }

    public static function schemaNameFromClassName(string $className): string
    {
        return u($className)
            ->lower()
            ->replace(' ', '_')
            ->replace('\\', '.')
            ->toString();
    }
}