<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Entity\Route;
use Ddrv\Router\Exception\MethodNotAllowed;
use Ddrv\Router\Exception\RouteNotFound;
use Ddrv\Router\Service\PatternParser;
use Ddrv\Router\Service\RouteCollector;
use Ddrv\Router\Service\RouteDispatcher;
use PHPUnit\Framework\TestCase;

final class RouteDispatcherTest extends TestCase
{
    public function testDispatch()
    {
        $routeCollector = new RouteCollector();
        $patternParser = new PatternParser();

        $routeDispatcher = new RouteDispatcher($routeCollector, $patternParser);

        $route1 = new Route(['OPTIONS'], '/json-rpc', 'jsonRpcOptionsHandler');

        $route2 = new Route(['POST'], '/json-rpc', 'jsonRpcHandler');

        $route3 = new Route(['GET'], '/rest/users/{userId}', 'UsersListHandler');
        $route3->where('userId', '\d+');

        $routeCollector->add($route1);
        $routeCollector->add($route2);
        $routeCollector->add($route3);

        try {
            $routeDispatcher->dispatch('GET', '/json-rpc');
            $this->fail(sprintf('Failed asserting that exception of type "%s" is thrown.', MethodNotAllowed::class));
        } catch (MethodNotAllowed $exception) {
            $this->assertSame(['OPTIONS', 'POST'], $exception->getAllowedMethods());
        }

        $dispatchResult1 = $routeDispatcher->dispatch('OPTIONS', '/json-rpc');
        $this->assertSame('jsonRpcOptionsHandler', $dispatchResult1->getRoute()->getHandler());

        $dispatchResult2 = $routeDispatcher->dispatch('POST', '/json-rpc');
        $this->assertSame('jsonRpcHandler', $dispatchResult2->getRoute()->getHandler());

        $dispatchResult3 = $routeDispatcher->dispatch('GET', '/rest/users/3');
        $this->assertSame('UsersListHandler', $dispatchResult3->getRoute()->getHandler());
        $this->assertSame(['userId' => '3'], $dispatchResult3->getPathParameters());

        $dispatchResult4 = $routeDispatcher->dispatch('HEAD', '/rest/users/4');
        $this->assertSame('UsersListHandler', $dispatchResult4->getRoute()->getHandler());
        $this->assertSame(['userId' => '4'], $dispatchResult4->getPathParameters());

        try {
            $routeDispatcher->dispatch('DELETE', '/api');
            $this->fail(sprintf('Failed asserting that exception of type "%s" is thrown.', RouteNotFound::class));
        } catch (RouteNotFound $exception) {
            $this->assertTrue(true);
        }
    }
}
