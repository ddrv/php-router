<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteGroupInterface;
use Ddrv\Router\Service\RouteCollector;
use PHPUnit\Framework\TestCase;

abstract class RouteGroupTestCase extends TestCase
{
    final public function testRoute(): void
    {
        $this->runMethod('route', ['GET', 'POST']);
    }

    final public function testGet(): void
    {
        $this->runMethod('get');
    }

    final public function testPost(): void
    {
        $this->runMethod('post');
    }

    final public function testPut(): void
    {
        $this->runMethod('put');
    }

    final public function testPatch(): void
    {
        $this->runMethod('patch');
    }

    final public function testDelete(): void
    {
        $this->runMethod('delete');
    }

    final public function testOptions(): void
    {
        $this->runMethod('options');
    }

    final public function testAny(): void
    {
        $this->runMethod('any');
    }

    final public function testGroup(): void
    {
        $routeCollector = new RouteCollector();
        $routeRegister = $this->getRouteRegister($routeCollector, '/api');
        $routeRegister->group('/v1', function (RouteGroupInterface $routeRegister) {
            $routeRegister->group('/users/{userLogin}', function (RouteGroupInterface $routeRegister) {
                $routeRegister->get('/posts/{postSlug}', 'handler01')->name('route01');
                $routeRegister->group('/posts/{postSlug}', function (RouteGroupInterface $routeRegister) {
                    $routeRegister->post('/comments', 'handler02')->name('route02');
                });
            });
        });

        $route01 = $routeCollector->findByName('route01');
        $this->assertSame(['GET'], $route01->getMethods());
        $this->assertSame('handler01', $route01->getHandler());
        $this->assertSame('/api/v1/users/{userLogin}/posts/{postSlug}', $route01->getPattern());

        $route02 = $routeCollector->findByName('route02');
        $this->assertSame(['POST'], $route02->getMethods());
        $this->assertSame('handler02', $route02->getHandler());
        $this->assertSame('/api/v1/users/{userLogin}/posts/{postSlug}/comments', $route02->getPattern());
    }

    final public function testMiddleware(): void
    {
        $routeCollector = new RouteCollector();
        $routeRegister = $this->getRouteRegister($routeCollector);
        $routeRegister->group('/group01', function (RouteGroupInterface $routeRegister) {
            $routeRegister->any('/pattern01', 'handler01')
                ->name('route01')
                ->middleware('route01/01')
                ->middleware('route01/02')
            ;
            $routeRegister->any('/pattern02', 'handler02')
                ->name('route02')
                ->middleware('route02/01')
                ->middleware('route02/02')
            ;
        })->middleware('group02/01')->middleware('group02/02');
        $routeRegister->middleware('group01/01')->middleware('group01/02');

        $route01 = $routeCollector->findByName('route01');
        $expectedMiddlewares = ['route01/01', 'route01/02', 'group02/01', 'group02/02', 'group01/01', 'group01/02'];

        $this->assertSame($expectedMiddlewares, $route01->getMiddlewares());
    }

    final public function testWhere(): void
    {
        $routeCollector = new RouteCollector();
        $routeRegister = $this->getRouteRegister($routeCollector, '/api');
        $routeRegister->group('/v{apiVersion}', function (RouteGroupInterface $routeRegister) {
            $routeRegister->group('/users/{userLogin}', function (RouteGroupInterface $routeRegister) {
                $routeRegister->group('/posts/{postSlug}', function (RouteGroupInterface $routeRegister) {
                    $routeRegister->get('/comments/{commentId}', 'handler01')
                        ->name('route01')
                        ->where('commentId', '[1-9][0-9]*')
                    ;
                })->where('postSlug', '[a-z\-0-9]+');
            })->where('userLogin', '[a-z0-9]{5,32}');
        })->where('apiVersion', '(1|2|3)');

        $route01 = $routeCollector->findByName('route01');

        $parameterRegexps = $route01->getParameterRegexps();
        $this->assertCount(4, $parameterRegexps);
        $this->assertArrayHasKey('commentId', $parameterRegexps);
        $this->assertArrayHasKey('postSlug', $parameterRegexps);
        $this->assertArrayHasKey('userLogin', $parameterRegexps);
        $this->assertArrayHasKey('apiVersion', $parameterRegexps);

        $this->assertSame('[1-9][0-9]*', $parameterRegexps['commentId']);
        $this->assertSame('[a-z\-0-9]+', $parameterRegexps['postSlug']);
        $this->assertSame('[a-z0-9]{5,32}', $parameterRegexps['userLogin']);
        $this->assertSame('(1|2|3)', $parameterRegexps['apiVersion']);
    }

    private function runMethod(string $method, array $methods = []): void
    {
        $routeCollector = new RouteCollector();
        $routeRegister = $this->getRouteRegister($routeCollector, '');

        switch ($method) {
            case 'route':
                $params = [$methods, '/pattern01', 'handler01'];
                break;
            case 'any':
                $methods = ['*'];
                $params = ['/pattern01', 'handler01'];
                break;
            default:
                $params = ['/pattern01', 'handler01'];
                $methods = [strtoupper($method)];
        }
        $callable = [$routeRegister, $method];
        $callable(...$params)->name('route01')->middleware('route01/01');

        $route01 = $routeCollector->findByName('route01');
        $this->assertSame($methods, $route01->getMethods());
        $this->assertSame('handler01', $route01->getHandler());
        $this->assertSame('/pattern01', $route01->getPattern());
        $this->assertSame('route01', $route01->getName());
        $this->assertSame(['route01/01'], $route01->getMiddlewares());
    }

    abstract protected function getRouteRegister(
        RouteCollectorInterface $routeCollector,
        string $prefix = ''
    ): RouteGroupInterface;
}
