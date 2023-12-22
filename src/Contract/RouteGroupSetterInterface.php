<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface RouteGroupSetterInterface
{
    /**
     * @param string $parameter
     * @param string $regexp
     * @return RouteGroupSetterInterface
     */
    public function where(string $parameter, string $regexp): self;

    /**
     * @param mixed $middleware
     * @return RouteGroupSetterInterface
     */
    public function middleware($middleware): self;
}
