<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\Extra\MediaTypeMap;
use DeadMansSwitch\OpenApi\Schema\V3_0\MediaType;
use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Schema\V3_0\Operation;
use DeadMansSwitch\OpenApi\Schema\V3_0\Reference;
use DeadMansSwitch\OpenApi\Schema\V3_0\RequestBody;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use DeadMansSwitch\OpenApi\Symfony\Util\Namer;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Route;

final class RequestBodyProcessor implements RouteProcessorInterface
{
    public function __construct(
        private readonly RouteProcessorUtils $utils,
        private readonly SchemaMapperInterface $mapper,
    ) {}

    /**
     * @throws ReflectionException
     * @throws UnprocessableRouteException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        $handler           = $this->utils->getRouteHandlerReflectionMethod($route);
        $requestReflection = $this->getRequestBodyReflection($handler);

        if ($requestReflection === null) {
            return;
        }

        $name   = Namer::schemaNameFromClassName($requestReflection->getName());
        $schema = $this->mapper->map($requestReflection);

        $openapi->components->schemas[$name] = $schema;

        foreach ($route->getMethods() as $httpMethod) {
            $method    = strtolower($httpMethod);
            $operation = $openapi->paths[$route->getPath()]->{$method} ?? new Operation();
            $body      = $this->buildRequestBodySchema($requestReflection->getName());

            $operation->requestBody = $body;

            $openapi->paths[$route->getPath()]->{$method} = $operation;
        }
    }

    private function getRequestBodyReflection(ReflectionMethod $handler): ?ReflectionClass
    {
        $class = null;

        foreach ($handler->getParameters() as $parameter) {
            foreach ($parameter->getAttributes() as $attribute) {
                if ($attribute->getName() !== MapRequestPayload::class) {
                    continue;
                }

                $class = $parameter->getType()->getName();
            }
        }

        if ($class === null || !class_exists($class)) {
            return null;
        }

        return new ReflectionClass($class);
    }

    private function buildRequestBodySchema(string $className): RequestBody
    {
        return new RequestBody(
            content: MediaTypeMap::fromArray([
                'application/json' => new MediaType(
                    schema: new Reference(
                        ref: Namer::schema($className)
                    )
                )
            ]),
        );
    }
}