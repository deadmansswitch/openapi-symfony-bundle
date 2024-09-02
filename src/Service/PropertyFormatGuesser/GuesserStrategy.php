<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception\FormatNotGuessedException;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Exception\InvalidGuesserException;
use ReflectionProperty;

final class GuesserStrategy implements GuesserStrategyInterface
{
    public function __construct(public readonly iterable $guessers) {}

    /**
     * @throws FormatNotGuessedException
     * @throws InvalidGuesserException
     */
    public function guess(ReflectionProperty $property): string
    {
        foreach ($this->guessers as $guesser) {
            if (!$guesser instanceof GuesserInterface) {
                throw new InvalidGuesserException('Guesser must implement GuesserInterface');
            }

            $format = $guesser->guess($property);

            if ($format !== null) {
                return $format;
            }
        }

        throw new FormatNotGuessedException();
    }
}