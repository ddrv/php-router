<?php

declare(strict_types=1);

namespace Ddrv\Router\Wrapper;

use Ddrv\Router\Contract\RouteGroupInterface;
use Ddrv\Router\Contract\RouteGroupSetterInterface;
use Ddrv\Router\Contract\RouteSetterInterface;

final class RouteGroupWrapper implements RouteGroupInterface
{
    private RouteGroupInterface $routeGroup;

    public function __construct(RouteGroupInterface $routeGroup)
    {
        $this->routeGroup = $routeGroup;
    }

    /**
     * @inheritDoc
     */
    public function group(string $pattern, callable $callable): RouteGroupSetterInterface
    {
        return $this->routeGroup->group($pattern, $callable);
    }

    /**
     * @inheritDoc
     */
    public function get(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->get($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->post($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->put($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->patch($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->delete($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function options(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->options($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function any(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->any($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function route(array $methods, string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeGroup->route($methods, $pattern, $handler);
    }
}
