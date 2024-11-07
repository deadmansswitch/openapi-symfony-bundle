<?php

declare(strict_types=1);

namespace DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Processors;

use DeadMansSwitch\OpenApi\Schema\V3_0\OpenApi;
use DeadMansSwitch\OpenApi\Symfony\Attribute\Tag;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Exception\UnprocessableRouteException;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\RouteProcessorInterface;
use DeadMansSwitch\OpenApi\Symfony\Service\RouteProcessor\Util\RouteProcessorUtils;
use DeadMansSwitch\OpenApi\Types\MapOfStrings;
use ReflectionAttribute;
use ReflectionException;
use Symfony\Component\Routing\Route;

final class TagProcessor implements RouteProcessorInterface
{
    public function __construct(private readonly RouteProcessorUtils $utils) {}

    /**
     * @throws ReflectionException
     * @throws UnprocessableRouteException
     */
    public function process(OpenApi &$openapi, Route $route): void
    {
        $tags = [];

        $handler = $this->utils->getRouteHandlerReflectionMethod($route);

        foreach ($handler->getAttributes() as $attribute) {
            $res = $this->getTagsFromAttribute($attribute);
            $tags = [...$tags, ...$res];
        }

        foreach ($handler->getDeclaringClass()->getAttributes() as $attribute) {
            $res = $this->getTagsFromAttribute($attribute);
            $tags = [...$tags, ...$res];
        }

        foreach ($route->getMethods() as $httpMethod) {
            $method = strtolower($httpMethod);

            $openapi
                ->paths[$route->getPath()]
                ->{$method}
                ->tags = MapOfStrings::fromArray($tags);
        }
    }

    private function getTagsFromAttribute(ReflectionAttribute $attribute): array
    {
        $tags = [];

        if ($attribute->getName() === Tag::class) {
            $args = $attribute->getArguments();
            $name = $args['name'] ?? null;

            if ($name !== null) {
                $tags[] = $name;
            }
        }

        return $tags;
    }
}