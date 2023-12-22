<?php

declare(strict_types=1);

namespace Ddrv\Router\Contract;

use Ddrv\Router\Exception\ParametersRequired;
use Ddrv\Router\Exception\UndefinedRoute;

interface UriGeneratorInterface
{
    /**
     * @param string $routeName
     * @param array<string, string> $pathParameters
     * @param array $queryParameters
     * @param string|null $prefix
     * @return string
     * @throws UndefinedRoute
     * @throws ParametersRequired
     */
    public function uri(
        string $routeName,
        array $pathParameters = [],
        array $queryParameters = [],
        ?string $prefix = null
    ): string;
}
