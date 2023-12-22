<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\PatternParserInterface;
use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteInterface;
use Ddrv\Router\Entity\DispatchResult;
use RuntimeException;

final class RouteCollector implements RouteCollectorInterface
{
    /**
     * @var array<RouteInterface>
     */
    private array $routes = [];
    /**
     * @var array<string, RouteInterface>
     */
    private array $byName = [];
    /**
     * @var array<string, RouteInterface[]>
     */
    private array $staticRoutes = [];
    /**
     * @var array<non-empty-string, RouteInterface[]>
     */
    private array $regexpRoutes = [];
    private bool $isNamesIndexed = false;
    private bool $isPatternsIndexed = false;

    /**
     * @inheritDoc
     */
    public function add(RouteInterface $route): void
    {
        $this->routes[] = $route;
        $this->reindex();
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name): ?RouteInterface
    {
        $this->indexNames();
        return $this->byName[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function findByPath(string $path, PatternParserInterface $patternParser): iterable
    {
        $this->indexPatterns($patternParser);

        if (array_key_exists($path, $this->staticRoutes)) {
            foreach ($this->staticRoutes[$path] as $route) {
                yield new DispatchResult($route, []);
            }
            return;
        }

        foreach ($this->regexpRoutes as $regexp => $routes) {
            foreach ($routes as $route) {
                $parameterNames = $patternParser->getParameterNames($route->getPattern());
                $pathParameters = $this->match($regexp, $path, $parameterNames);
                if (!is_null($pathParameters)) {
                    yield new DispatchResult($route, $pathParameters);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function reindex(): void
    {
        $this->isNamesIndexed = false;
        $this->isPatternsIndexed = false;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): iterable
    {
        foreach ($this->routes as $route) {
            yield $route;
        }
    }

    private function indexNames(): void
    {
        if ($this->isNamesIndexed) {
            return;
        }

        $this->byName = [];

        foreach ($this->routes as $route) {
            $this->indexByName($route);
        }
        $this->isNamesIndexed = true;
    }

    private function indexPatterns(PatternParserInterface $patternParser): void
    {
        if ($this->isPatternsIndexed) {
            return;
        }

        $this->regexpRoutes = [];
        $this->staticRoutes = [];

        foreach ($this->routes as $route) {
            $this->indexByPattern($route, $patternParser);
        }
        $this->isPatternsIndexed = true;
    }

    private function indexByName(RouteInterface $route): void
    {
        $name = $route->getName();
        if (is_null($name)) {
            return;
        }

        $this->byName[$name] = $route;
    }

    private function indexByPattern(RouteInterface $route, PatternParserInterface $patternParser): void
    {
        $pattern = $route->getPattern();

        $parsedPattern = $patternParser->parse($pattern, $route->getParameterRegexps());
        $parameterNames = $parsedPattern->getParameterNames();

        if (empty($parameterNames)) {
            $this->staticRoutes[$pattern][] = $route;
            return;
        }

        $this->regexpRoutes[$parsedPattern->getRegexp()][] = $route;
    }

    /**
     * @param non-empty-string $regexp
     * @param string $path
     * @param string[] $parameters
     * @return array<string, string>|null
     */
    private function match(string $regexp, string $path, array $parameters): ?array
    {
        $matches = [];
        $isMatch = preg_match($regexp, $path, $matches);
        if ($isMatch !== 1) {
            return null;
        }

        $result = [];
        foreach ($parameters as $parameter) {
            if (!array_key_exists($parameter, $matches)) {
                throw new RuntimeException('Parameter not exists');
            }
            $result[$parameter] = $matches[$parameter];
        }
        return $result;
    }
}
