<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\ParametersMap;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Parameter;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\ExtractorInterface;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\Routing\Route;

final class PathEntityIdentifierExtractor implements ExtractorInterface
{
    public function supports(Route $route, ReflectionMethod $handler): bool
    {
        $path       = $route->getPath();
        $openBrace  = strpos($path, '{');
        $closeBrace = strpos($path, '}');

        return $openBrace !== false && $closeBrace !== false;
    }

    public function extract(Route $route, ReflectionMethod $handler): ParametersMap
    {
        $result   = ParametersMap::fromArray([]);
        $compiled = $route->compile();

        // 1. get all variables from route and mapping to method arguments if exists
        $variables = $compiled->getPathVariables();
        $mapping   = $route->getDefault('_route_mapping') ?? [];

        // 2. Map over handler parameters to get its names to prevent nested looping
        $parameters = [];
        foreach ($handler->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $parameter;
        }

        // 3. iterate over variables and process them
        foreach ($variables as $variable) {
            $name = $mapping[$variable] ?? $variable;

            $parameter = $parameters[$name] ?? null;
            if (!$parameter instanceof ReflectionParameter) {
                continue;
            }

            $type = 'number';

            $parameter = new Parameter(
                name: $variable,
                in: 'path',
                required: true,
                allowEmptyValue: false,
                schema: new Schema(type: $type),
            );

            $result[] = $parameter;
        }

        return $result;
    }
}