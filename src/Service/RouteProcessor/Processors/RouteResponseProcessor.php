<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\Extra\MediaTypeMap;
use DeadMansSwitch\OpenApi\Schema\V3_0\MediaType;
use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Schema\V3_0\Operation;
use DeadMansSwitch\OpenApi\Schema\V3_0\PathItem;
use DeadMansSwitch\OpenApi\Schema\V3_0\Reference;
use DeadMansSwitch\OpenApi\Schema\V3_0\Response;
use DeadMansSwitch\OpenApi\Schema\V3_0\Responses;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\SchemaComponentMissedException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use DeadMansSwitch\OpenApi\Symfony\Util\Namer;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

final class RouteResponseProcessor implements RouteProcessorInterface
{
    public function __construct(private readonly RouteProcessorUtils $utils) {}

    /**
     * @throws ReflectionException
     * @throws UnprocessableRouteException
     * @throws SchemaComponentMissedException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        // Get path item from $openapi object
        $path = $openapi->paths[$route->getPath()];
        if (!$path instanceof PathItem) {
            throw new SchemaComponentMissedException("PathItem missed for route {$route->getPath()}");
        }

        // Build key for openapi.schema.components
        $class = $this->getReturnTypeClassName($route);
        $key   = Namer::schemaNameFromClassName($class);

        // Check is schema exists in openapi.schema.components
        if (!$openapi->components->schemas->offsetExists(offset: $key)) {
            throw new SchemaComponentMissedException("Schema component missed for {$key}");
        }

        // Create reference object
        $refString = Namer::schema(classname: $class);
        $reference = new Reference(ref: $refString);

        // For each HTTP Method in route create responses object
        foreach ($route->getMethods() as $httpMethod) {
            $responses = Responses::fromArray([
                200 => $this->getSuccessfulResponseObject($reference),
            ]);

            $operation = $this->extendOperationWithResponses(
                path: $path,
                method: $httpMethod,
                responses: $responses,
            );

            $path->{strtolower($httpMethod)} = $operation;
        }

        $openapi->paths[$route->getPath()] = $path;
    }

    /**
     * @throws UnprocessableRouteException
     * @throws ReflectionException
     */
    private function getReturnTypeClassName(Route $route): string
    {
        $reflection = $this->utils->getRouteHandlerReflectionMethod($route);
        $return     = $reflection->getReturnType();

        return $return->getName();
    }

    private function getSuccessfulResponseObject(Reference $reference): Response
    {
        return new Response(
            description: "Successful response",
            content: MediaTypeMap::fromArray([
                'application/json' => new MediaType(
                    schema: $reference,
                ),
            ]),
        );
    }

    private function extendOperationWithResponses(PathItem $path, string $method, Responses $responses): Operation
    {
        $operation = $path->{strtolower($method)} ?? new Operation();
        $operation->responses = $responses;

        return $operation;
    }
}