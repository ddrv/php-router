<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Entity\Route;
use Ddrv\Router\Exception\ParametersRequired;
use Ddrv\Router\Exception\UndefinedRoute;
use Ddrv\Router\Service\PatternParser;
use Ddrv\Router\Service\RouteCollector;
use Ddrv\Router\Service\UriGenerator;
use PHPUnit\Framework\TestCase;

final class UriGeneratorTest extends TestCase
{
    public function testUri()
    {
        $routeCollector = new RouteCollector();
        $patternParser = new PatternParser();

        $route1 = new Route(['OPTIONS'], '/json-rpc', 'jsonRpcOptionsHandler');
        $route1->name('route01');

        $route2 = new Route(['POST'], '/json-rpc/v{version}', 'jsonRpcHandler');
        $route2->where('version', '(1|2|3)')->name('route02');

        $route3 = new Route(['GET'], '/rest/users/{userId}', 'UsersListHandler');
        $route3->name('route03')->where('userId', '\d+');

        $routeCollector->add($route1);
        $routeCollector->add($route2);
        $routeCollector->add($route3);

        $uriGenerator = new UriGenerator($routeCollector, $patternParser);

        $this->assertSame('/json-rpc', $uriGenerator->uri('route01'));
        $this->assertSame(
            'http://localhost:8080/json-rpc',
            $uriGenerator->uri('route01', [], [], 'http://localhost:8080')
        );
        $this->assertSame(
            'http://localhost:8080/json-rpc',
            $uriGenerator->uri('route01', ['foo' => 'bar'], [], 'http://localhost:8080')
        );
        $this->assertSame(
            'http://localhost:8080/json-rpc?foo=bar',
            $uriGenerator->uri('route01', [], ['foo' => 'bar'], 'http://localhost:8080')
        );

        $this->assertSame('/json-rpc/v1', $uriGenerator->uri('route02', ['version' => '1']));
        $this->assertSame('/rest/users/1', $uriGenerator->uri('route03', ['userId' => '1']));

        try {
            $uriGenerator->uri('route04');
            $this->fail(sprintf('Failed asserting that exception of type "%s" is thrown.', UndefinedRoute::class));
        } catch (UndefinedRoute $exception) {
            $this->assertTrue(true);
        }

        try {
            $uriGenerator->uri('route03');
            $this->fail(sprintf('Failed asserting that exception of type "%s" is thrown.', ParametersRequired::class));
        } catch (ParametersRequired $exception) {
            $this->assertSame(['userId'], $exception->getRequiredParameters());
        }
    }
}
