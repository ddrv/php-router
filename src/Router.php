<?php

declare(strict_types=1);

namespace Ddrv\Router;

use Ddrv\Router\Contract\DispatchResultInterface;
use Ddrv\Router\Contract\PatternParserInterface;
use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteDispatcherInterface;
use Ddrv\Router\Contract\RouteGroupSetterInterface;
use Ddrv\Router\Contract\RouteRegisterInterface;
use Ddrv\Router\Contract\RouteSetterInterface;
use Ddrv\Router\Contract\UriGeneratorInterface;
use Ddrv\Router\Service\PatternParser;
use Ddrv\Router\Service\PatternParserCacheDecorator;
use Ddrv\Router\Service\RouteCollector;
use Ddrv\Router\Service\RouteDispatcher;
use Ddrv\Router\Service\RouteRegister;
use Ddrv\Router\Service\UriGenerator;

final class Router implements RouteDispatcherInterface, UriGeneratorInterface, RouteRegisterInterface
{
    private RouteDispatcherInterface $routeDispatcher;
    private RouteRegisterInterface $routeRegister;
    private UriGeneratorInterface $uriGenerator;

    public function __construct(
        string $prefix = '',
        ?RouteCollectorInterface $routeCollector = null,
        ?PatternParserInterface $patternParser = null
    ) {
        if (is_null($routeCollector)) {
            $routeCollector = new RouteCollector();
        }
        if (is_null($patternParser)) {
            $patternParser = new PatternParserCacheDecorator(new PatternParser());
        }
        $this->routeRegister = new RouteRegister($routeCollector, $prefix);
        $this->routeDispatcher = new RouteDispatcher($routeCollector, $patternParser);
        $this->uriGenerator = new UriGenerator($routeCollector, $patternParser);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(string $method, string $path): DispatchResultInterface
    {
        return $this->routeDispatcher->dispatch($method, $path);
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
        return $this->uriGenerator->uri($routeName, $pathParameters, $queryParameters, $prefix);
    }

    /**
     * @inheritDoc
     */
    public function group(string $pattern, callable $callable): RouteGroupSetterInterface
    {
        return $this->routeRegister->group($pattern, $callable);
    }

    /**
     * @inheritDoc
     */
    public function get(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->get($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->post($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->put($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->patch($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->delete($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function options(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->options($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function any(string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->any($pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function route(array $methods, string $pattern, $handler): RouteSetterInterface
    {
        return $this->routeRegister->route($methods, $pattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteGroupSetterInterface
    {
        return $this->routeRegister->where($parameter, $regexp);
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteGroupSetterInterface
    {
        return $this->routeRegister->middleware($middleware);
    }
}
