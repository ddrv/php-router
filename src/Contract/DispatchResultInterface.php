<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface DispatchResultInterface
{
    /**
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface;

    /**
     * @return array<string, string>
     */
    public function getPathParameters(): array;
}
