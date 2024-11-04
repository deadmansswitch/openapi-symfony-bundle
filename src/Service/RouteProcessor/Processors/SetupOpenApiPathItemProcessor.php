<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Schema\V3_0\PathItem;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use Symfony\Component\Routing\Route;

final class SetupOpenApiPathItemProcessor implements RouteProcessorInterface
{
    public function process(OpenApi &$openapi, Route $route): void
    {
        $item = $openapi->paths->offsetExists($route->getPath())
            ? $openapi->paths->offsetGet($route->getPath())
            : new PathItem();

        $openapi->paths->offsetSet($route->getPath(), $item);
    }
}