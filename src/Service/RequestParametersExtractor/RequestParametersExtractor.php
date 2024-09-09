<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\ParametersMap;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

final class RequestParametersExtractor implements RequestParametersExtractorInterface
{
    // TODO:
    //  - PathBackedEnumExtractor
    //  - PathScalarValueExtractor
    //  - QueryParamAttributeExtractor
    //  - QueryStringAttributeExtractor
    public function __construct(private readonly iterable $extractors) {}

    public function extract(Route $route, ReflectionMethod $handler): ParametersMap
    {
        $map = [];

        foreach ($this->extractors as $extractor) {
            assert($extractor instanceof ExtractorInterface);

            if (!$extractor->supports($route, $handler)) {
                continue;
            }

            // TODO: merge ParametersMap from extractor ($res) with parameters map in state of current method ($map)
            $res = $extractor->extract($route, $handler);

            foreach ($res as $parameter) {
                $map[] = $parameter;
            }
        }

        return ParametersMap::fromArray($map);
    }
}