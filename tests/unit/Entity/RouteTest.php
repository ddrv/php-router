<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Entity;

use Ddrv\Router\Entity\Route;
use PHPUnit\Framework\TestCase;
use Tests\Ddrv\Router\Tools\TestCallback;

final class RouteTest extends TestCase
{
    /**
     * @param string[] $methods
     * @param string $pattern
     * @param $handler
     * @return void
     * @dataProvider provideCreate
     */
    public function testCreate(array $methods, string $pattern, $handler): void
    {
        $route = $this->createRoute($methods, $pattern, $handler);
        $this->assertSame($pattern, $route->getPattern());
        $this->assertSame($handler, $route->getHandler());
        $actualMethods = $route->getMethods();
        $this->assertCount(count($methods), $actualMethods);
        foreach ($methods as $method) {
            $this->assertTrue(in_array($method, $actualMethods));
        }

        $this->assertSame(null, $route->getName());
        $this->assertTrue(empty($route->getMiddlewares()));
        $this->assertTrue(empty($route->getParameterRegexps()));
    }

    /**
     * @return void
     */
    public function testName(): void
    {
        $route = $this->createRoute(['GET'], '/hello/{username}', null);

        $name = 'some.name';
        $route->name($name);
        $this->assertSame($name, $route->getName());
    }

    /**
     * @return void
     */
    public function testMiddleware(): void
    {
        $route = $this->createRoute(['GET'], '/hello/{username}', null);

        $middleware1 = 'mw1';
        $middleware2 = function () {
        };

        $route->middleware($middleware1);
        $route->middleware($middleware2);

        $middlewares = $route->getMiddlewares();
        $this->assertCount(2, $middlewares);
        $this->assertSame($middleware1, $middlewares[0]);
        $this->assertSame($middleware2, $middlewares[1]);
    }

    /**
     * @return void
     */
    public function testParameters(): void
    {
        $route = $this->createRoute(['GET'], '/api/v{version}/users/{login}/posts/{postId}/comments', null);

        $route->where('version', '(1|2|3)')->where('login', '[a-z0-9]{4,32}');

        $parameters = $route->getParameterRegexps();
        $this->assertCount(2, $parameters);
        $this->assertArrayHasKey('version', $parameters);
        $this->assertSame('(1|2|3)', $parameters['version']);
        $this->assertArrayHasKey('login', $parameters);
        $this->assertSame('[a-z0-9]{4,32}', $parameters['login']);
    }

    /**
     * @return void
     */
    public function testGroupParameters(): void
    {
        $route = $this->createRoute(['GET'], '/api/v{version}/users/{login}/posts/{postId}/comments', null);

        $route->addParameterFromGroup(1, 'version', '(1|2|3)');
        $route->addParameterFromGroup(2, 'login', '[a-z0-9]{4,32}');
        $route->where('postId', '\d+');

        $parameters = $route->getParameterRegexps();
        $this->assertCount(3, $parameters);
        $this->assertArrayHasKey('version', $parameters);
        $this->assertSame('(1|2|3)', $parameters['version']);
        $this->assertArrayHasKey('login', $parameters);
        $this->assertSame('[a-z0-9]{4,32}', $parameters['login']);
        $this->assertArrayHasKey('postId', $parameters);
        $this->assertSame('\d+', $parameters['postId']);
    }

    /**
     * @return void
     */
    public function testGroupMiddlewares(): void
    {
        $route = $this->createRoute(['GET'], '/api/v{version}/users/{login}/posts/{postId}/comments', null);

        $route->middleware('route01/01');
        $route->addMiddlewareFromGroup(1, 'group01/01');
        $route->addMiddlewareFromGroup(2, 'group02/01');
        $route->addMiddlewareFromGroup(2, 'group02/02');
        $route->addMiddlewareFromGroup(1, 'group01/02');
        $route->middleware('route01/02');

        $middlewares = $route->getMiddlewares();
        $this->assertCount(6, $middlewares);
        $this->assertSame('route01/01', $middlewares[0]);
        $this->assertSame('route01/02', $middlewares[1]);
        $this->assertSame('group02/01', $middlewares[2]);
        $this->assertSame('group02/02', $middlewares[3]);
        $this->assertSame('group01/01', $middlewares[4]);
        $this->assertSame('group01/02', $middlewares[5]);
    }

    public function testCallback(): void
    {
        $route = $this->createRoute(['GET'], '/hello/{username}', 'hello');
        $callback = new TestCallback();
        $route->setCallback($callback);

        $this->assertSame(0, $callback->getCalls());
        $route->where('username', '\w+');
        $this->assertSame(1, $callback->getCalls());
        $route->where('username', '\w{2,10}');
        $this->assertSame(2, $callback->getCalls());
        $route->middleware('middleware1');
        $this->assertSame(3, $callback->getCalls());
        $route->name('hello.username');
        $this->assertSame(4, $callback->getCalls());
    }

    /**
     * @return iterable<array{0:string[], 1:string, 2:mixed}>
     */
    public static function provideCreate(): iterable
    {
        $fn = function () {
        };
        return [
            [['*'], '/any', 'handler-as-string'],
            [['GET', 'POST'], '/get-and-post', $fn],
            [['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], '/all', ['key' => 'value']],
        ];
    }

    /**
     * @param string[] $methods
     * @param string $pattern
     * @param $handler
     * @return Route
     */
    private function createRoute(array $methods, string $pattern, $handler): Route
    {
        return new Route($methods, $pattern, $handler);
    }
}
