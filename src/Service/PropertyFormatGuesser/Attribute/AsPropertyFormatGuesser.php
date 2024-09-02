<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class AsPropertyFormatGuesser
{
    public function __construct(public readonly int $priority = 0) {}
}