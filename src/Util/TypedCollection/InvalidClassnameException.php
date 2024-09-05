<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Util;

use InvalidArgumentException;

final class InvalidClassnameException extends InvalidArgumentException
{
    public function __construct(string $classname)
    {
        parent::__construct("Invalid classname: {$classname}");
    }
}