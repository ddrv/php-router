<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

use Ddrv\Router\Exception\MethodNotAllowed;
use Ddrv\Router\Exception\RouteNotFound;

interface RouteDispatcherInterface
{
    /**
     * @param string $method
     * @param string $path
     * @return DispatchResultInterface
     * @throws RouteNotFound
     * @throws MethodNotAllowed
     */
    public function dispatch(string $method, string $path): DispatchResultInterface;
}
