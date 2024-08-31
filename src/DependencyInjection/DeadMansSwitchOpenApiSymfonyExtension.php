<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DeadMansSwitchOpenApiSymfonyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void {}
}