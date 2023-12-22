<?php

declare(strict_types=1);

namespace Ddrv\Router\Entity;

use Ddrv\Router\Contract\DispatchResultInterface;
use Ddrv\Router\Contract\RouteInterface;

final class DispatchResult implements DispatchResultInterface
{
    private RouteInterface $route;
    /**
     * @var array<string, string>
     */
    private array $pathParameters;

    /**
     * @param RouteInterface $route
     * @param array<string, string> $pathParameters
     */
    public function __construct(
        RouteInterface $route,
        array $pathParameters
    ) {
        $this->route = $route;
        $this->pathParameters = $pathParameters;
    }

    /**
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * @return array<string, string>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }
}
