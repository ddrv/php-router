<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface RouteGroupInterface
{
    /**
     * @param string $pattern
     * @param callable(RouteGroupInterface):void $callable
     * @return RouteGroupSetterInterface
     */
    public function group(string $pattern, callable $callable): RouteGroupSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function get(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function post(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function put(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function patch(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function delete(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function options(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function any(string $pattern, $handler): RouteSetterInterface;

    /**
     * @param string[] $methods
     * @param string $pattern
     * @param mixed $handler
     * @return RouteSetterInterface
     */
    public function route(array $methods, string $pattern, $handler): RouteSetterInterface;
}
