<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor;

use DeadMansSwitch\OpenAPI\Schema\V3_0\Extra\ParametersMap;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

interface ExtractorInterface
{
    public const TAG = 'dead_mans_switch.openapi.symfony.request_parameters_concrete_extractor';

    public function supports(Route $route, ReflectionMethod $handler): bool;

    public function extract(Route $route, ReflectionMethod $handler): ParametersMap;
}