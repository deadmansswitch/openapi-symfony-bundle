<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use Symfony\Component\Routing\Route;

interface RouteProcessorInterface
{
    public function process(OpenApi &$openapi, Route $route): void;
}