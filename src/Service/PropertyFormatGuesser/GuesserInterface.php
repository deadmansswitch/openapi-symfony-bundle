<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser;

use ReflectionProperty;

interface GuesserInterface
{
    public function next(): ?GuesserInterface;
    public function guess(ReflectionProperty $property): Format;
}

// EmailGuesser.php
// HostnameGuesser.php
// IPv4Guesser.php
// IPv6Guesser.php
// IriGuesser.php
// UriGuesser.php
// UuidGuesser.php