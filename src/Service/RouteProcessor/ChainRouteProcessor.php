<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors as Processors;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use Symfony\Component\Routing\Route;
use Throwable;

final class ChainRouteProcessor implements RouteProcessorInterface
{
    private readonly array $processors;

    public function __construct(
        private readonly SchemaMapperInterface $mapper,
        private readonly RouteProcessorUtils $utils,
    ) {
        $this->processors = [
            // Validate, that we can process this route
            new Processors\RouteValidationProcessor(utils: $this->utils),

            // Create if not exists path item for openapi spec
            new Processors\SetupOpenApiPathItemProcessor(),

            // Create output schema for route
            new Processors\OutputSchemaProcessor(mapper: $this->mapper, utils: $this->utils),

            // Create responses map for route
            new Processors\RouteResponseProcessor(utils: $this->utils),

            // Process input params (query params, request body)
            // TODO: ...

            // Process open api tags
            // TODO: ...

            // Run user-defined processors to override or extend auto-generated specification
            // TODO: ...
        ];
    }

    /**
     * @throws UnprocessableRouteException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        foreach ($this->processors as $processor) {
            assert($processor instanceof RouteProcessorInterface);

            try {
                $processor->process($openapi, $route);
            } catch (Throwable) {
                return;
            }
        }
    }
}