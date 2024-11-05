<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

final class RouteValidationProcessor implements RouteProcessorInterface
{
    public function __construct(private readonly RouteProcessorUtils $utils) {}

    /**
     * Process route and throw exception if it can not be processed by the bundle.
     * We're expects that route is handled by controller method that will return
     * a DTO class that is not a subclass of Symfony's Response.
     *
     * @throws UnprocessableRouteException
     * @throws ReflectionException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        $returnType = $this
            ->utils
            ->getRouteHandlerReflectionMethod($route)
            ->getReturnType()
            ?->getName()
        ;

        if ($returnType === null) {
            throw new UnprocessableRouteException();
        }

        if (is_a($returnType, Response::class, true)) {
            throw new UnprocessableRouteException();
        }
    }
}