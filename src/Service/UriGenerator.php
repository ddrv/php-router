<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\PatternParserInterface;
use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\UriGeneratorInterface;
use Ddrv\Router\Exception\ParametersRequired;
use Ddrv\Router\Exception\UndefinedRoute;

final class UriGenerator implements UriGeneratorInterface
{
    private RouteCollectorInterface $routeCollector;
    private PatternParserInterface $patternParser;

    public function __construct(
        RouteCollectorInterface $routeCollector,
        PatternParserInterface $patternParser
    ) {
        $this->routeCollector = $routeCollector;
        $this->patternParser = $patternParser;
    }

    /**
     * @inheritDoc
     */
    public function uri(
        string $routeName,
        array $pathParameters = [],
        array $queryParameters = [],
        ?string $prefix = null
    ): string {
        $route = $this->routeCollector->findByName($routeName);

        if (is_null($route)) {
            throw new UndefinedRoute(sprintf('route %s not defined', $routeName));
        }

        $pattern = $route->getPattern();
        $parameterNames = $this->patternParser->getParameterNames($pattern);
        $required = [];

        $pairs = [];
        foreach ($parameterNames as $parameterName) {
            if (!array_key_exists($parameterName, $pathParameters)) {
                $required[] = $parameterName;
                continue;
            }
            $pairs[$this->patternParser->wrapParameter($parameterName)] = $pathParameters[$parameterName];
        }

        if (!empty($required)) {
            throw new ParametersRequired($required, sprintf('parameters %s is required', implode(', ', $required)));
        }

        $uri = strtr($pattern, $pairs);
        if (!empty($queryParameters)) {
            $uri = sprintf('%s?%s', $uri, http_build_query($queryParameters));
        }
        if (!is_null($prefix)) {
            $uri = sprintf('%s/%s', rtrim($prefix, '/'), ltrim($uri, '/'));
        }

        return $uri;
    }
}
