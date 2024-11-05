<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util;

use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

final class RouteProcessorUtils
{
    /**
     * @throws UnprocessableRouteException
     */
    public function getRouteHandlerClassReflection(Route $route): ReflectionClass
    {
        // TODO: add memoization

        $controller = $route->getDefault('_controller');

        $segments = explode('::', $controller);
        $class    = $segments[0];

        if (!class_exists($class)) {
            throw new UnprocessableRouteException();
        }

        return new ReflectionClass($class);
    }

    /**
     * @throws UnprocessableRouteException
     * @throws ReflectionException
     */
    public function getRouteHandlerReflectionMethod(Route $route): ReflectionMethod
    {
        // TODO: add memoization

        $handler = $route->getDefault('_controller');

        if (empty($handler) || !is_string($handler)) {
            throw new UnprocessableRouteException("{$route->getPath()} missed valid controller");
        }

        $segments = explode('::', $handler);
        $class    = $segments[0];
        $method   = $segments[1] ?? '__invoke';

        return new ReflectionMethod($class, $method);
    }
}