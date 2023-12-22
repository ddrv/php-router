<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface RouteSetterInterface
{
    /**
     * @param string $name
     * @return RouteSetterInterface
     */
    public function name(string $name): self;

    /**
     * @param string $parameter
     * @param string $regexp
     * @return RouteSetterInterface
     */
    public function where(string $parameter, string $regexp): self;

    /**
     * @param mixed $middleware
     * @return RouteSetterInterface
     */
    public function middleware($middleware): self;
}
