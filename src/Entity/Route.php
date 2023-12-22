<?php

declare(strict_types=1);

namespace Ddrv\Router\Entity;

use Ddrv\Router\Contract\RouteInterface;
use Ddrv\Router\Contract\RouteSetterInterface;

final class Route implements RouteSetterInterface, RouteInterface
{
    /**
     * @var array<string>
     */
    private array $methods;
    private string $pattern;
    /**
     * @var mixed
     */
    private $handler;
    private ?string $name = null;
    /**
     * @var array<string, string>
     */
    private array $parameters = [];

    /**
     * @var array<int, array<string, string>>
     */
    private array $groupParameters = [];
    /**
     * @var array
     */
    private array $middlewares = [];
    /**
     * @var array<int, array>
     */
    private array $groupMiddlewares = [];
    /**
     * @var callable|null
     */
    private $callback = null;

    /**
     * @param array<string> $methods
     * @param string $pattern
     * @param mixed $handler
     */
    public function __construct(array $methods, string $pattern, $handler)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @inheritDoc
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getParameterRegexps(): array
    {
        ksort($this->groupParameters);
        $parameters = [];
        foreach ($this->groupParameters as $groupParameters) {
            $parameters = array_replace($parameters, $groupParameters);
        }
        return array_replace($parameters, $this->parameters);
    }

    /**
     * @inheritDoc
     */
    public function getMiddlewares(): array
    {
        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($this->middlewares as $middleware) {
            $result[] = $middleware;
        }

        krsort($this->groupMiddlewares);
        foreach ($this->groupMiddlewares as $middlewares) {
            /**
             * @psalm-suppress MixedAssignment
             */
            foreach ($middlewares as $middleware) {
                $result[] = $middleware;
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function name(string $name): RouteSetterInterface
    {
        $this->name = $name;
        $this->change();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function where(string $parameter, string $regexp): RouteSetterInterface
    {
        $this->parameters[$parameter] = $regexp;
        $this->change();
        return $this;
    }

    public function addParameterFromGroup(int $level, string $parameter, string $regexp): void
    {
        $this->groupParameters[$level][$parameter] = $regexp;
        $this->change();
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): RouteSetterInterface
    {
        $this->middlewares[] = $middleware;
        $this->change();
        return $this;
    }

    /**
     * @param int $level
     * @param mixed $middleware
     * @return void
     */
    public function addMiddlewareFromGroup(int $level, $middleware): void
    {
        $this->groupMiddlewares[$level][] = $middleware;
        $this->change();
    }

    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    private function change(): void
    {
        if (is_null($this->callback)) {
            return;
        }
        ($this->callback)();
    }
}
