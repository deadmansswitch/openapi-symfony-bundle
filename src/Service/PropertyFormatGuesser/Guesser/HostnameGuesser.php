<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception\FormatNotGuessedException;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Format;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;
use ReflectionProperty;

final class HostnameGuesser implements GuesserInterface
{
    private ?GuesserInterface $nextGuesser = null;

    public function setNextGuesser(?GuesserInterface $guesser): void
    {
        $this->nextGuesser = $guesser;
    }

    public function getNextGuesser(): ?GuesserInterface
    {
        return $this->nextGuesser;
    }

    /**
     * @throws FormatNotGuessedException
     */
    public function guess(ReflectionProperty $property): Format
    {
        if ($property->getName() === 'hostname') {
            return Format::Hostname;
        }

        $format = $this->getNextGuesser()?->guess($property);

        if ($format instanceof Format) {
            return $format;
        }

        throw new FormatNotGuessedException();
    }
}