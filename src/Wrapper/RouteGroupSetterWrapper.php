<?php

declare(strict_types=1);

namespace Ddrv\Router\Wrapper;

use Ddrv\Router\Contract\RouteGroupSetterInterface;

final class RouteGroupSetterWrapper implements RouteGroupSetterInterface
{
    private RouteGroupSetterInterface $routeGroupSetter;

    public function __construct(RouteGroupSetterInterface $routeGroupSetter)
    {
        $this->routeGroupSetter = $routeGroupSetter;
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteGroupSetterInterface
    {
        $this->routeGroupSetter->where($parameter, $regexp);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteGroupSetterInterface
    {
        $this->routeGroupSetter->middleware($middleware);
        return $this;
    }
}
