<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser as Guesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception\FormatNotGuessedException;
use ReflectionProperty;

final class HostnameGuesser implements Guesser\GuesserInterface
{
    public function __construct(private readonly ?Guesser\GuesserInterface $nextGuesser) {}

    public function next(): ?Guesser\GuesserInterface
    {
        return $this->nextGuesser;
    }

    /**
     * @throws FormatNotGuessedException
     */
    public function guess(ReflectionProperty $property): Guesser\Format
    {
        if ($property->getName() === 'hostname') {
            return Guesser\Format::Hostname;
        }

        $result = $this->next()?->guess($property);

        if ($result instanceof Guesser\Format) {
            return $result;
        }

        throw new Guesser\Exception\FormatNotGuessedException();
    }
}