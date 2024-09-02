<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Tests;

use DeadMansSwitch\OpenApi\Symfony\DeadMansSwitchOpenApiSymfonyBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class TestingKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        yield new DeadMansSwitchOpenApiSymfonyBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void {}
}