<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Tests\Service\PropertyFormatGuesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserStrategyInterface;
use Symfony\Component\Validator\Constraints as Assert;

test( 'Guesser can guess email format', function () {
    $guesser = $this->container->get(GuesserStrategyInterface::class);

    $class = new class {
        #[Assert\Email]
        public string $email;
    };

    $format = $guesser->guess(new \ReflectionProperty($class, 'email'));

    expect($format)->toBe('email');
});