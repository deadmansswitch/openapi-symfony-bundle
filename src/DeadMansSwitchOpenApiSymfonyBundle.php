<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony;

use DeadMansSwitch\OpenApi\Symfony\DependencyInjection\ActionParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DeadMansSwitchOpenApiSymfonyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ActionParserCompilerPass());
    }
}