<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Entity;

use Ddrv\Router\Contract\RouteInterface;
use Ddrv\Router\Entity\DispatchResult;
use Ddrv\Router\Entity\Route;
use PHPUnit\Framework\TestCase;
use Tests\Ddrv\Router\Unit\Service\RouteTrait;

final class DispatchResultTest extends TestCase
{
    use RouteTrait;

    /**
     * @param RouteInterface $route
     * @param array<string, string> $pathParameters
     * @return void
     * @dataProvider provideCreate
     */
    public function testCreate(RouteInterface $route, array $pathParameters): void
    {
        $dispatchResult = new DispatchResult($route, $pathParameters);
        $this->assertSame($route, $dispatchResult->getRoute());
        $this->assertSame($pathParameters, $dispatchResult->getPathParameters());
    }

    /**
     * @return iterable<array{0:Route, 1:array<string, string>}>
     */
    public static function provideCreate(): iterable
    {
        $params = [
            ['foo' => 'bar'],
            ['a' => '1', 'b' => '2', 'c' => '3', 'd' => '4'],
        ];
        foreach ($params as $pathParameters) {
            yield [self::generateRouteWithParams(array_keys($pathParameters)), $pathParameters];
        }
    }
}
