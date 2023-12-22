<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteGroupInterface;
use Ddrv\Router\Contract\RouteGroupSetterInterface;
use Ddrv\Router\Contract\RouteSetterInterface;
use Ddrv\Router\Entity\Route;
use Ddrv\Router\Wrapper\RouteGroupSetterWrapper;
use Ddrv\Router\Wrapper\RouteGroupWrapper;
use Ddrv\Router\Wrapper\RouteSetterWrapper;
use Ddrv\Router\Wrapper\RouteWrapper;

final class RouteGroup implements RouteGroupInterface, RouteGroupSetterInterface
{
    private string $prefix;
    private int $level;
    private RouteCollectorInterface $routeCollector;
    /**
     * @var array<int, mixed>
     */
    private array $middlewares = [];
    /**
     * @var array<string, string>
     */
    private array $parameters = [];
    /**
     * @var callable|null
     */
    private $callback = null;
    private RouteGroupSetterWrapper $routeGroupSetter;
    /**
     * @var Route[]
     */
    private array $routes = [];
    /**
     * @var RouteGroup[]
     */
    private array $routeGroups = [];

    public function __construct(
        RouteCollectorInterface $routeCollector,
        string $prefix,
        int $level
    ) {
        $this->routeCollector = $routeCollector;
        $this->prefix = $prefix;
        $this->level = $level;
        $this->routeGroupSetter = new RouteGroupSetterWrapper($this);
    }

    /**
     * @inheritDoc
     */
    public function group(string $pattern, callable $callable): RouteGroupSetterInterface
    {
        $routeGroup = new self($this->routeCollector, $this->prefix . $pattern, $this->level + 1);
        if (is_callable($this->callback)) {
            $routeGroup->setCallback($this->callback);
        }
        $callable(new RouteGroupWrapper($routeGroup));
        $this->routeGroups[] = $routeGroup;
        return new RouteGroupSetterWrapper($routeGroup);
    }

    /**
     * @inheritDoc
     */
    public function get(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['GET'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['POST'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['PUT'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['PATCH'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['DELETE'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function options(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['OPTIONS'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function any(string $pattern, $handler): RouteSetterInterface
    {
        return $this->route(['*'], $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function route(array $methods, string $pattern, $handler): RouteSetterInterface
    {
        $route = new Route($methods, $this->prefix . $pattern, $handler);
        if (is_callable($this->callback)) {
            $route->setCallback($this->callback);
        }
        foreach ($this->parameters as $parameter => $regexp) {
            $route->addParameterFromGroup($this->level, $parameter, $regexp);
        }
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($this->middlewares as $middleware) {
            $route->addMiddlewareFromGroup($this->level, $middleware);
        }
        $this->routeCollector->add(new RouteWrapper($route));
        $this->routes[] = $route;
        return new RouteSetterWrapper($route);
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteGroupSetterInterface
    {
        $this->parameters[$parameter] = $regexp;
        foreach ($this->routes as $route) {
            $route->addParameterFromGroup($this->level, $parameter, $regexp);
        }
        foreach ($this->routeGroups as $routeGroup) {
            $routeGroup->where($parameter, $regexp);
        }
        return $this->routeGroupSetter;
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteGroupSetterInterface
    {
        $this->middlewares[] = $middleware;
        foreach ($this->routes as $route) {
            $route->addMiddlewareFromGroup($this->level, $middleware);
        }
        foreach ($this->routeGroups as $routeGroup) {
            $routeGroup->middleware($middleware);
        }
        return $this->routeGroupSetter;
    }

    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
        foreach ($this->routes as $route) {
            $route->setCallback($callback);
        }
        foreach ($this->routeGroups as $routeGroup) {
            $routeGroup->setCallback($callback);
        }
    }
}
