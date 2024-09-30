<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor\Extractor;

use BackedEnum;
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

            // if parameter is scalar type -> let's add to parameters
            $type = $parameter->getType();

            if ($type->isBuiltin()) {
                $output[] = new Parameter(
                    name: $parameter->getName(),
                    in: 'query',
                    required: $this->isRequired($parameter),
                    schema: $this->mapper->map($parameter),
                    example: $this->getExample($parameter),
                );

                continue;
            }

            // if parameter is enum -> let's add to parameters
            if (enum_exists($type->getName())) {
                $output[] = new Parameter(
                    name: $parameter->getName(),
                    in: 'query',
                    required: $this->isRequired($parameter),
                    schema: $this->mapper->map($parameter),
                    example: $this->getExample($parameter),
                );

                continue;
            }

            // if parameter is object -> let's iterate over properties and add each of them to parameters
            if (class_exists($type->getName())) {
                $dtoRef         = new ReflectionClass($type->getName());
                $dtoConstructor = $dtoRef->getMethod('__construct');

                foreach ($dtoConstructor->getParameters() as $dtoConstructorParameter) {
                    $output[] = new Parameter(
                        name: $dtoConstructorParameter->name,
                        in: 'query',
                        required: $this->isRequired($dtoConstructorParameter),
                        schema: $this->mapper->map($dtoConstructorParameter),
                        example: $this->getExample($dtoConstructorParameter),
                    );
                }
            }
        }

        return $output;
    }

    private function isRequired(ReflectionParameter $parameter): bool
    {
        return $parameter->allowsNull() === false;
    }

    private function getExample(ReflectionParameter $parameter): null|string
    {
        $default = $parameter->isDefaultValueAvailable()
            ? $parameter->getDefaultValue()
            : null;

        if (is_scalar($default)) {
            return (string) $default;
        }

        if ($default instanceof BackedEnum) {
            return $default->value;
        }

        return null;
    }
}