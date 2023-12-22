<?php

declare(strict_types=1);

namespace Ddrv\Router\Wrapper;

use Ddrv\Router\Contract\RouteInterface;

final class RouteWrapper implements RouteInterface
{
    private RouteInterface $route;

    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->route->getMethods();
    }

    /**
     * @inheritDoc
     */
    public function getPattern(): string
    {
        return $this->route->getPattern();
    }

    /**
     * @inheritDoc
     */
    public function getHandler()
    {
        return $this->route->getHandler();
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->route->getName();
    }

    /**
     * @inheritDoc
     */
    public function getParameterRegexps(): array
    {
        return $this->route->getParameterRegexps();
    }

    /**
     * @inheritDoc
     */
    public function getMiddlewares(): array
    {
        return $this->route->getMiddlewares();
    }
}
