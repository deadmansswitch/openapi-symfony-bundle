<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Tests\Service\PropertyFormatGuesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;
use Symfony\Component\Validator\Constraints as Assert;

test('Guesser can guess email format', function () {
    $class = new class {
        #[Assert\Email]
        public string $email;
    };

    $guesser = $this->container->get(GuesserInterface::class);
    $format  = $guesser->guess($class, 'email');

    ray($format)->green();

    expect(true)->toBeTrue();
});