<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser;

use ReflectionProperty;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser as Guesser;

final class EmailGuesser implements Guesser\GuesserInterface
{
    public function __construct(private readonly ?Guesser\GuesserInterface $next) {}

    public function next(): ?Guesser\GuesserInterface
    {
        return $this->next;
    }

    /**
     * @throws Guesser\Exception\FormatNotGuessedException
     */
    public function guess(ReflectionProperty $property): Guesser\Format
    {
        if ($property->getName() === 'email') {
            return Guesser\Format::Email;
        }

        if (!$this->next() instanceof Guesser\GuesserInterface) {
            throw new Guesser\Exception\FormatNotGuessedException();
        }

        return $this->next->guess($property);
    }
}