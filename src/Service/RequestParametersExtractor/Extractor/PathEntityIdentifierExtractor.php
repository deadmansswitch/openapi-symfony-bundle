<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor;

use DeadMansSwitch\OpenApi\Schema\V3_0\Extra\ParametersMap;
use DeadMansSwitch\OpenApi\Schema\V3_0\Parameter;
use DeadMansSwitch\OpenApi\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\ExtractorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper\TypeMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Symfony\Component\Routing\Route;
use Symfony\Component\Uid\Uuid;

final class PathEntityIdentifierExtractor implements ExtractorInterface
{
    public function __construct(
        private readonly TypeMapperInterface $typeMapper,
        private readonly SchemaMapperInterface $schemaMapper,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function supports(Route $route, ReflectionMethod $handler): bool
    {
        $path       = $route->getPath();
        $openBrace  = strpos($path, '{');
        $closeBrace = strpos($path, '}');

        return $openBrace !== false && $closeBrace !== false;
    }

    /**
     * @throws Exception
     */
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

            $result[] = new Parameter(
                name: $variable,
                in: 'path',
                required: true,
                allowEmptyValue: false,
                schema: $this->getParameterSchema($parameter),
            );
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function getParameterSchema(ReflectionParameter $parameter): Schema
    {
        $type = $parameter->getType()?->getName();

        if ($type === null) {
            throw new Exception('Parameter type is missed');
        }

        if (!class_exists($type)) {
            return new Schema(type: $this->typeMapper->getOpenApiType($type));
        }

        if ($type === Uuid::class) {
            return new Schema(type: 'string', format: 'uuid');
        }

        $meta       = $this->entityManager->getClassMetadata($type);
        $identifier = $meta->getIdentifier();
        if (empty($identifier)) {
            throw new Exception('Entity identifier is missed');
        }

        // TODO: let's keep only non-complex identifier for now

        $idReference = new ReflectionProperty(class: $type, property: $identifier[0]);

        return $this->schemaMapper->map($idReference);
    }
}