<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\RequestParametersExtractorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors as Processors;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use ReflectionClass;
use Symfony\Component\Routing\Route;
use Throwable;

final class ChainRouteProcessor implements RouteProcessorInterface
{
    private readonly array $processors;

    public function __construct(
        private readonly array $config,
        private readonly SchemaMapperInterface $mapper,
        private readonly RouteProcessorUtils $utils,
        private readonly RequestParametersExtractorInterface $extractor,
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

            // Process input params query params
            new Processors\QueryParamProcessor(utils: $this->utils, extractor: $this->extractor),

            // Process request body
            new Processors\RequestBodyProcessor(utils: $this->utils, mapper: $this->mapper),

            // Process open api tags
            new Processors\TagProcessor(utils: $this->utils),

            // Run user-defined processors to override or extend auto-generated specification
            // TODO: ...
        ];
    }

    /**
     * @throws UnprocessableRouteException
     * @throws \ReflectionException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        if (!$this->isRouteIsProcessable($route)) {
            return;
        }

        foreach ($this->processors as $processor) {
            assert($processor instanceof RouteProcessorInterface);

            try {
                $processor->process($openapi, $route);
            } catch (Throwable) {
                return;
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function isRouteIsProcessable(Route $route): bool
    {
        $directories = $this->config['directories'] ?? [];

        if (empty($directories)) {
            return true;
        }

        $controller = $route->getDefault('_controller');

        if (!class_exists($controller)) {
            return false;
        }

        $ref = new ReflectionClass($controller);

        $processable = false;

        foreach ($directories as $directory) {
            if (str_starts_with(haystack: $ref->getFileName(), needle: $directory)) {
                $processable = true;
                break;
            }
        }

        return $processable;
    }
}