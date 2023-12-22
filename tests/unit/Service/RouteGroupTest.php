<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Service\RouteCollector;
use Ddrv\Router\Service\RouteGroup;
use Tests\Ddrv\Router\Tools\TestCallback;

final class RouteGroupTest extends RouteGroupTestCase
{
    public function testProxyCallable(): void
    {
        $routeGroup = $this->getRouteRegister(new RouteCollector());
        $callback = new TestCallback();
        $routeGroup->setCallback($callback);
        $routeGroup->any('/pattern01', 'handler01')->name('route01')->middleware('own/01');
        $routeGroup->any('/pattern02', 'handler02')->name('route02')->middleware('own/02');

        $this->assertSame(4, $callback->getCalls());
    }

    protected function getRouteRegister(RouteCollectorInterface $routeCollector, string $prefix = ''): RouteGroup
    {
        return new RouteGroup($routeCollector, $prefix, 1);
    }
}
