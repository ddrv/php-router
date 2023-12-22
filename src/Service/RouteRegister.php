<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteGroupSetterInterface;
use Ddrv\Router\Contract\RouteRegisterInterface;
use Ddrv\Router\Contract\RouteSetterInterface;

final class RouteRegister implements RouteRegisterInterface
{
    private RouteGroup $mainGroup;

    public function __construct(
        RouteCollectorInterface $routeCollector,
        string $prefix = ''
    ) {
        $this->mainGroup = new RouteGroup($routeCollector, $prefix, 0);
        $this->mainGroup->setCallback(function () use ($routeCollector) {
            $routeCollector->reindex();
        });
    }

    /**
     * @inheritDoc
     */
    public function group(string $pattern, callable $callable): RouteGroupSetterInterface
    {
        return $this->mainGroup->group($pattern, $callable);
    }

    /**
     * @inheritDoc
     */
    public function get(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->get($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->post($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->put($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->patch($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->delete($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function options(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->options($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function any(string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->any($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function route(array $methods, string $pattern, $handler): RouteSetterInterface
    {
        return $this->mainGroup->route($methods, $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteGroupSetterInterface
    {
        return $this->mainGroup->where($parameter, $regexp);
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteGroupSetterInterface
    {
        return $this->mainGroup->middleware($middleware);
    }
}
