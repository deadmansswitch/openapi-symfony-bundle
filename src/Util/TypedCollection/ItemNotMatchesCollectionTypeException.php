<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Util\TypedCollection;

use InvalidArgumentException;

final class ItemNotMatchesCollectionTypeException extends InvalidArgumentException
{
    public function __construct(string $collection, string $expected, string $provided)
    {
        parent::__construct("Collection {$collection} expects all items of type {$expected}, but one or more items of type {$provided} was provided");
    }
}