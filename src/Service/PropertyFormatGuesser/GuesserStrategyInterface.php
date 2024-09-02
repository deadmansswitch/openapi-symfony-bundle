<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser;

use ReflectionProperty;

interface GuesserStrategyInterface
{
    public function guess(ReflectionProperty $property): string;
}