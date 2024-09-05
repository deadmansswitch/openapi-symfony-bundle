<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Util\TypedCollection;

use DeadMansSwitch\OpenApi\Symfony\Util\InvalidClassnameException;
use IteratorAggregate;
use Traversable;

abstract class TypedCollection implements IteratorAggregate
{
    private function __construct(private readonly array $items)
    {
        $type = static::getItemClassname();

        if (!class_exists($type)) {
            throw new InvalidClassnameException($type);
        }

        foreach ($items as $item) {
            if (!is_a($item, $type, true)) {
                throw new ItemNotMatchesCollectionTypeException(
                    collection: static::class,
                    expected: $type,
                    provided: is_object($item) ? get_class($item) : gettype($item),
                );
            }
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }

    public static function fromArray(array $items): TypedCollection
    {
        return new static($items);
    }

    abstract public static function getItemClassname(): string;
}