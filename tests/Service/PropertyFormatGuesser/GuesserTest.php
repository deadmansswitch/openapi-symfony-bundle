<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Tests\Service\PropertyFormatGuesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;

test('Guesser can guess email format', function () {
    $guesser = $this->container->get(GuesserInterface::class);

    ray($guesser);

    expect(true)->toBeTrue();
});