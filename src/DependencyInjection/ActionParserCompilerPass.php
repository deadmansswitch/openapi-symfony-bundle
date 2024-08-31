<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ActionParserCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void {}
}