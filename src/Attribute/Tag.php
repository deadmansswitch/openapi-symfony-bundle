<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::TARGET_METHOD|\Attribute::IS_REPEATABLE)]
final class Tag
{
    public function __construct(public readonly string $name) {}
}