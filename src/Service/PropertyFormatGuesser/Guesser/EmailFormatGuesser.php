<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Format;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints\Email;

final class EmailFormatGuesser implements GuesserInterface
{
    public function guess(ReflectionProperty $property): ?string
    {
        if ($property->getType()->getName() !== 'string') {
            return null;
        }

        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === Email::class) {
                return Format::Email->value;
            }
        }

        return null;
    }
}