<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

interface RouteInterface
{
    /**
     * @return array<string>
     */
    public function getMethods(): array;

    /**
     * @return string
     */
    public function getPattern(): string;

    /**
     * @return mixed
     */
    public function getHandler();

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return array<string, string>
     */
    public function getParameterRegexps(): array;

    /**
     * @return array
     */
    public function getMiddlewares(): array;
}
