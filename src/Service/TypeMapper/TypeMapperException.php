<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper;

use Exception;

final class TypeMapperException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}