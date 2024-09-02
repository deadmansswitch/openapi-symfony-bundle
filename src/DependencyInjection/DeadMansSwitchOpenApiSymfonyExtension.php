<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser\EmailGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser\HostnameGuesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\Guesser\IPv4Guesser;
use DeadMansSwitch\OpenApi\Symfony\Service\PropertyFormatGuesser\GuesserInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DeadMansSwitchOpenApiSymfonyExtension extends Extension
{
    private const TAG_PROPERTY_FORMAT_GUESSER = 'dead_mans_switch.openapi.symfony.property_format_guesser';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->registerPropertyTypeGuesserChain($container);
    }

    private function registerPropertyTypeGuesserChain(ContainerBuilder $container): void
    {
        $container->register(EmailGuesser::class, EmailGuesser::class)->addTag(self::TAG_PROPERTY_FORMAT_GUESSER);
        $container->register(HostnameGuesser::class, HostnameGuesser::class)->addTag(self::TAG_PROPERTY_FORMAT_GUESSER);
        $container->register(IPv4Guesser::class, IPv4Guesser::class)->addTag(self::TAG_PROPERTY_FORMAT_GUESSER);
        $container->registerForAutoconfiguration(GuesserInterface::class)->addTag(self::TAG_PROPERTY_FORMAT_GUESSER);

        $guessers = $container->findTaggedServiceIds(self::TAG_PROPERTY_FORMAT_GUESSER);
        $ordered  = [];

        foreach ($guessers as $id => $attributes) {
            $priority = (int) ($attributes[0]['priority'] ?? 0);
            $ordered[$priority][] = $id;
        }

        $sorted = $this->sortServiceDefinitions($ordered);
        $first  = array_shift($sorted);

        $container->setAlias(GuesserInterface::class, $first)->setPublic(true);

        $guesserDefinition = $container->getDefinition($first);
        foreach ($sorted as $serviceId) {
            $nextGuesserDefinition = $container->getDefinition($serviceId);
            $guesserDefinition->addMethodCall('setNextGuesser', [$nextGuesserDefinition]);
            $guesserDefinition = $nextGuesserDefinition;
        }
    }

    private function sortServiceDefinitions(array $orderedDefinitions): array
    {
        ksort($orderedDefinitions);

        return array_merge(...$orderedDefinitions);
    }
}