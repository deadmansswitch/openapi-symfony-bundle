<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnsupportedReturnTypeException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use DeadMansSwitch\OpenApi\Symfony\Util\Namer;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionType;
use Symfony\Component\Routing\Route;

final class OutputSchemaProcessor implements RouteProcessorInterface
{
    public function __construct(
        private readonly SchemaMapperInterface $mapper,
        private readonly RouteProcessorUtils $utils,
    ) {}

    /**
     * @throws UnprocessableRouteException
     * @throws ReflectionException
     * @throws UnsupportedReturnTypeException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        $reflection = $this->utils->getRouteHandlerReflectionMethod($route);

        // Get return type for the handler
        $outputType = $this->getReturnType($reflection);

        // Convert return type to OpenApi schema
        $schema = $this->buildSchema($outputType);
        if (!$schema instanceof Schema) {
            return;
        }

        // Generate schema name
        $name = $this->generateName($outputType);

        // Store Schema object into OpenApi object
        $this->storeSchema($openapi, $name, $schema);
    }

    /**
     * @throws ReflectionException
     * @throws UnprocessableRouteException
     */
    private function getReturnType(ReflectionMethod $reflectionMethod): ReflectionType
    {
        $returnType = $reflectionMethod->getReturnType();

        if ($returnType === null) {
            throw new UnprocessableRouteException("Missed return type");
        }

        return $returnType;
    }

    /**
     * @throws UnsupportedReturnTypeException
     * @throws ReflectionException
     */
    private function buildSchema(ReflectionType $returnType): Schema|null
    {
        $type = $returnType->getName();

        if ($returnType->isBuiltin()) {
            if ($type === 'void') {
                return null;
            }

            throw new UnsupportedReturnTypeException("Return type {$type} is not supported yet");
        }

        $ref = new ReflectionClass($type);

        return $this->mapper->map($ref);
    }

    private function generateName(ReflectionType $returnType): string
    {
        return Namer::schemaNameFromClassName($returnType->getName());
    }

    private function storeSchema(OpenApi &$openapi, string $name, Schema $schema): void
    {
        $openapi->components->schemas->offsetSet($name, $schema);
    }
}