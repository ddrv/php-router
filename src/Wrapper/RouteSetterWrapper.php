<?php

declare(strict_types=1);

namespace Ddrv\Router\Wrapper;

use Ddrv\Router\Contract\RouteSetterInterface;

final class RouteSetterWrapper implements RouteSetterInterface
{
    private RouteSetterInterface $routeSetter;

    public function __construct(RouteSetterInterface $routeSetter)
    {
        $this->routeSetter = $routeSetter;
    }

    /**
     * @inheritDoc
     */
    public function name(string $name): RouteSetterInterface
    {
        $this->routeSetter->name($name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteSetterInterface
    {
        $this->routeSetter->where($parameter, $regexp);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteSetterInterface
    {
        $this->routeSetter->middleware($middleware);
        return $this;
    }
}
