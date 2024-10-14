<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\ParametersMap;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Parameter;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Schema;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\ExtractorInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Route;

final class QueryParameterExtractor implements ExtractorInterface
{
    public function __construct(private readonly SchemaMapperInterface $mapper) {}

    public function supports(Route $route, ReflectionMethod $handler): bool
    {
        return !empty($handler->getParameters());
    }

    /**
     * @throws ReflectionException
     */
    public function extract(Route $route, ReflectionMethod $handler): ParametersMap
    {
        $result = [];

        $parameters = $handler->getParameters();

        foreach ($parameters as $parameter) {
            $attributes = $parameter->getAttributes();

            foreach ($attributes as $attribute) {
                if ($attribute->getName() !== MapQueryParameter::class) {
                    continue;
                }

                $result[] = new Parameter(
                    name: $this->getName($parameter),
                    in: 'query',
                    required: $this->isRequired($parameter),
                    schema: $this->getSchema($parameter),
                    example: $this->getExample($parameter),
                );
            }
        }

        return ParametersMap::fromArray($result);
    }

    private function isRequired(ReflectionParameter $parameter): bool
    {
        return $parameter->allowsNull() === false;
    }

    private function getName(ReflectionParameter $parameter): string
    {
        $result = $parameter->getName();

        foreach ($parameter->getAttributes() as $attribute) {
            if (!is_a($attribute->getName(), MapQueryParameter::class, true)) {
                continue;
            }

            $args = $attribute->getArguments();

            if (isset($args['name'])) {
                $result = $args['name'];
            }
        }

        return $result;
    }

    private function getExample(ReflectionParameter $parameter): null|string
    {
        return $parameter->isDefaultValueAvailable()
            ? (string) $parameter->getDefaultValue()
            : null;
    }

    private function getSchema(ReflectionParameter $parameter): Schema
    {
        return $this->mapper->map($parameter);
    }
}