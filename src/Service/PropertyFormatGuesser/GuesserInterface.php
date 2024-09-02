<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser;

use ReflectionProperty;

interface GuesserInterface
{
    public function guess(ReflectionProperty $property): ?string;
}

// EmailGuesser.php
// HostnameGuesser.php
// IPv4Guesser.php
// IPv6Guesser.php
// IriGuesser.php
// UriGuesser.php
// UuidGuesser.php