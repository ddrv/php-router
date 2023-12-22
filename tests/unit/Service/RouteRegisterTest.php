<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Service\RouteRegister;

final class RouteRegisterTest extends RouteGroupTestCase
{
    protected function getRouteRegister(RouteCollectorInterface $routeCollector, string $prefix = ''): RouteRegister
    {
        return new RouteRegister($routeCollector, $prefix);
    }
}
