<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\ParametersMap;
use DeadMansSwitch\OpenAPI\Schema\V3_0\Parameter;
use DeadMansSwitch\OpenApi\Symfony\Service\ReflectionSchemaMapper\SchemaMapperInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\ExtractorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Route;

final class QueryStringExtractor implements ExtractorInterface
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

        foreach ($handler->getParameters() as $parameter) {
            assert($parameter instanceof ReflectionParameter);

            foreach ($parameter->getAttributes() as $attribute)  {
                if ($attribute->getName() !== MapQueryString::class) {
                    continue;
                }

                foreach ($this->extractFieldsFromQueryStringObject($parameter) as $field) {
                    $result[] = $field;
                }
            }
        }

        return ParametersMap::fromArray($result);
    }

    /**
     * @throws ReflectionException
     */
    private function extractFieldsFromQueryStringObject(ReflectionParameter $parameter): array
    {
        $output = [];

        $class  = new ReflectionClass($parameter->getType()->getName());
        $method = $class->getMethod('__construct');

        foreach ($method->getParameters() as $parameter) {
            assert($parameter instanceof ReflectionParameter);

            $output[] = new Parameter(
                name: $parameter->getName(),
                in: 'query',
                required: $this->isRequired($parameter),
                schema: $this->mapper->map($parameter),
                example: $this->getExample($parameter),
            );
        }

        return $output;
    }

    private function isRequired(ReflectionParameter $parameter): bool
    {
        return $parameter->allowsNull() === false;
    }

    private function getExample(ReflectionParameter $parameter): null|string
    {
        return $parameter->isDefaultValueAvailable()
            ? (string) $parameter->getDefaultValue()
            : null;
    }
}