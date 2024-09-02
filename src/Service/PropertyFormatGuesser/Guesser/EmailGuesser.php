<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser;

use ReflectionProperty;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Format;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception\FormatNotGuessedException;

final class EmailGuesser implements GuesserInterface
{
    private ?GuesserInterface $next = null;

    public function setNextGuesser(?GuesserInterface $guesser): void
    {
        $this->next = $guesser;
    }

    public function getNextGuesser(): ?GuesserInterface
    {
        return $this->next;
    }

    /**
     * @throws FormatNotGuessedException
     */
    public function guess(ReflectionProperty $property): Format
    {
        if ($property->getName() === 'email') {
            return Format::Email;
        }

        $format = $this->getNextGuesser()?->guess($property);
        if ($format instanceof Format) {
            return $format;
        }

        throw new FormatNotGuessedException;
    }
}