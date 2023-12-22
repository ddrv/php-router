<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Entity\Route;
use Ddrv\Router\Service\PatternParser;
use Ddrv\Router\Service\RouteCollector;
use PHPUnit\Framework\TestCase;

final class RouteCollectorTest extends TestCase
{
    use RouteTrait;

    /**
     * @param Route $route
     * @param string $name
     * @return void
     * @dataProvider provideGetByName
     */
    public function testGetByNameBefore(Route $route, string $name)
    {
        $routeCollector = $this->createRouteCollector();
        $this->setRouteCallback($route, $routeCollector);
        $route->name($name);
        $routeCollector->add($route);

        $actualRoute = $routeCollector->findByName($name);
        $this->assertSame($route, $actualRoute);
    }

    /**
     * @param Route $route
     * @param string $name
     * @return void
     * @dataProvider provideGetByName
     */
    public function testGetByNameAfter(Route $route, string $name)
    {
        $routeCollector = $this->createRouteCollector();
        $this->setRouteCallback($route, $routeCollector);
        $routeCollector->add($route);
        $actualRoute = $routeCollector->findByName($name);
        $this->assertSame(null, $actualRoute);

        $route->name($name);

        $actualRoute = $routeCollector->findByName($name);
        $this->assertSame($route, $actualRoute);
    }

    /**
     * @param string $pattern
     * @param string $path
     * @param array $parameterRegexps
     * @param array $expectedPathParameters
     * @return void
     * @dataProvider provideGetByPathFound
     */
    public function testGetByPathFound(
        string $pattern,
        string $path,
        array $parameterRegexps,
        array $expectedPathParameters
    ) {
        $patternParser = new PatternParser();
        $routeCollector = $this->createRouteCollector();
        $route = new Route(['*'], $pattern, null);
        $this->setRouteCallback($route, $routeCollector);
        foreach ($parameterRegexps as $param => $regexp) {
            $route->where($param, $regexp);
        }

        $routeCollector->add($route);

        $dispatchResults = [];
        foreach ($routeCollector->findByPath($path, $patternParser) as $dispatchResult) {
            $dispatchResults[] = $dispatchResult;
        }
        $this->assertCount(1, $dispatchResults);
        $this->assertSame($route, $dispatchResults[0]->getRoute());
        $this->assertSame($expectedPathParameters, $dispatchResults[0]->getPathParameters());
    }

    /**
     * @param string $pattern
     * @param string $path
     * @param array $parameterRegexps
     * @return void
     * @dataProvider provideGetByPathNotFound
     */
    public function testGetByPathNotFound(string $pattern, string $path, array $parameterRegexps)
    {
        $patternParser = new PatternParser();
        $routeCollector = $this->createRouteCollector();
        $route = new Route(['*'], $pattern, null);
        $this->setRouteCallback($route, $routeCollector);
        foreach ($parameterRegexps as $param => $regexp) {
            $route->where($param, $regexp);
        }

        $routeCollector->add($route);

        $dispatchResults = [];
        foreach ($routeCollector->findByPath($path, $patternParser) as $dispatchResult) {
            $dispatchResults[] = $dispatchResult;
        }
        $this->assertCount(0, $dispatchResults);
    }

    public static function provideGetByName(): iterable
    {
        return [
            [self::generateRoute(), 'route1'],
            [self::generateRoute(), 'route2'],
            [self::generateRoute(), 'route3'],
        ];
    }

    public static function provideGetByPathFound(): iterable
    {
        return [
            ['/hello', '/hello', [], []],
            ['/hello/{user}', '/hello/world', [], ['user' => 'world']],
            ['/hello/{user}', '/hello/world', ['user' => '[a-z]+'], ['user' => 'world']],
        ];
    }

    public static function provideGetByPathNotFound(): iterable
    {
        return [
            ['/hello', '/hllo', []],
            ['/hello/{user}', '/hello/world/ok', []],
            ['/hello/{user}', '/hello/w0rld', ['user' => '[a-z]+']],
        ];
    }

    private function createRouteCollector(): RouteCollector
    {
        return new RouteCollector();
    }

    private function setRouteCallback(Route $route, RouteCollector $routeCollector): void
    {
        $route->setCallback(static function () use ($routeCollector) {
            $routeCollector->reindex();
        });
    }
}
