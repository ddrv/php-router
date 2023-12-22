<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

use Ddrv\Router\Exception\MethodNotAllowed;
use Ddrv\Router\Exception\RouteNotFound;
use Ddrv\Router\Exception\UndefinedRoute;

interface RouteCollectorInterface
{
    /**
     * @param RouteInterface $route
     * @return void
     */
    public function add(RouteInterface $route): void;

    /**
     * @param string $name
     * @return RouteInterface
     * @throws UndefinedRoute
     */
    public function findByName(string $name): ?RouteInterface;

    /**
     * @param string $path
     * @param PatternParserInterface $patternParser
     * @return iterable<DispatchResultInterface>
     * @throws RouteNotFound
     * @throws MethodNotAllowed
     */
    public function findByPath(string $path, PatternParserInterface $patternParser): iterable;

    /**
     * @return void
     */
    public function reindex(): void;

    /**
     * @return iterable<RouteInterface>
     */
    public function getRoutes(): iterable;
}
