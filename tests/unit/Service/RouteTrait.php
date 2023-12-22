<?php

declare(strict_types=1);

namespace Tests\Ddrv\Router\Unit\Service;

use Ddrv\Router\Entity\Route;

trait RouteTrait
{
    /**
     * @param mixed $handler
     * @return Route
     */
    private static function generateRoute($handler = null): Route
    {
        return self::generateRouteWithParams(self::generateParameterNames(), $handler);
    }


    /**
     * @param string[] $pathParameters
     * @param mixed $handler
     * @return Route
     */
    private static function generateRouteWithParams(array $pathParameters, $handler = null): Route
    {
        $methods = self::generateHttpMethods();
        $pattern = '/base';
        $i = 0;
        foreach ($pathParameters as $parameter) {
            $pattern = sprintf('%s/part%d/{%s}', $pattern, $i + 1, $parameter);
            $i++;
        }
        return new Route($methods, $pattern, $handler);
    }

    /**
     * @return string[]
     */
    private static function generateHttpMethods(): array
    {
        $methodsCount = rand(1, 7);
        if ($methodsCount === 7) {
            return ['*'];
        }
        $all = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        if ($methodsCount === 6) {
            return $all;
        }
        $result = [];
        for ($i = 0; $i < $methodsCount; $i++) {
            $key = array_rand($all);
            $result[] = $all[$key];
            unset($all[$key]);
        }
        return $result;
    }

    /**
     * @return string[]
     */
    private static function generateParameterNames(): array
    {
        $parametersCount = rand(1, 5);
        $result = [];
        for ($i = 0; $i < $parametersCount; $i++) {
            $result[] = sprintf('param%d', $i + 1);
        }
        return $result;
    }
}
