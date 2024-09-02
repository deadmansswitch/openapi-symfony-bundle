<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony;

use DeadMansSwitch\OpenApi\Symfony\DependencyInjection\DeadMansSwitchOpenApiSymfonyExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DeadMansSwitchOpenApiSymfonyBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DeadMansSwitchOpenApiSymfonyExtension();
    }
}