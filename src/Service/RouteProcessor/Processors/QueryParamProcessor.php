<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Schema\V3_0\Operation;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\RequestParametersExtractorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use ReflectionException;
use Symfony\Component\Routing\Route;

final class QueryParamProcessor implements RouteProcessorInterface
{
    public function __construct(
        private readonly RouteProcessorUtils $utils,
        private readonly RequestParametersExtractorInterface $extractor,
    ) {}

    /**
     * @throws ReflectionException
     * @throws UnprocessableRouteException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        $reflection = $this->utils->getRouteHandlerReflectionMethod($route);
        $parameters = $this->extractor->extract(route: $route, handler: $reflection);

        foreach ($route->getMethods() as $httpMethod) {
            $method = strtolower($httpMethod);

            $operation = $openapi->paths[$route->getPath()]->{$method} ?? new Operation();
            $operation->parameters = $parameters;

            $openapi->paths[$route->getPath()]->{$method} = $operation;
        }
    }
}