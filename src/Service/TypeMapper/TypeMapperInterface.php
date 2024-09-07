<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\TypeMapper;

interface TypeMapperInterface
{
    public function getOpenApiType(string $phpType): string;

    public function getPhpType(string $openApiType): string;
}