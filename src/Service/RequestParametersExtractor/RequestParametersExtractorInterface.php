<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RequestParametersExtractor;

use DeadMansSwitch\OpenApi\Schema\V3_0\Extra\ParametersMap;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

interface RequestParametersExtractorInterface
{
    public function extract(Route $route, ReflectionMethod $handler): ParametersMap;
}