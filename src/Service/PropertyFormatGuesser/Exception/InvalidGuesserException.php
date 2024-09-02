<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception;

use Exception;

final class InvalidGuesserException extends Exception
{
    public function __construct(string $message = 'Invalid guesser provided')
    {
        parent::__construct($message);
    }
}