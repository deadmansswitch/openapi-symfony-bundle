<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Util;

interface TypedCollectionInterface
{
    /**
     * Static constructor for collection of DTOs
     */
    static function fromArray(array $items): static;

    /**
     * Returns FQCN to type of item DTO in collection.
     *
     * Used for generating OpenAPI schema and validation of
     * collection items type
     */
    static function type(): string;
}