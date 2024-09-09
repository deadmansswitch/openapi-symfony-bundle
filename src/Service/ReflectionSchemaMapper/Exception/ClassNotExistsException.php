<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\Exception;

use Exception;

final class ClassNotExistsException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}