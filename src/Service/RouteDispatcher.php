<?php

declare(strict_types=1);

namespace Ddrv\Router\Service;

use Ddrv\Router\Contract\DispatchResultInterface;
use Ddrv\Router\Contract\PatternParserInterface;
use Ddrv\Router\Contract\RouteCollectorInterface;
use Ddrv\Router\Contract\RouteDispatcherInterface;
use Ddrv\Router\Exception\MethodNotAllowed;
use Ddrv\Router\Exception\RouteNotFound;

final class RouteDispatcher implements RouteDispatcherInterface
{
    private RouteCollectorInterface $routeCollector;
    private PatternParserInterface $patternParser;

    public function __construct(
        RouteCollectorInterface $routeCollector,
        PatternParserInterface $patternParser
    ) {
        $this->routeCollector = $routeCollector;
        $this->patternParser = $patternParser;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(string $method, string $path): DispatchResultInterface
    {
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $dispatchResults = $this->routeCollector->findByPath($path, $this->patternParser);
        $allowedMethods = [];
        foreach ($dispatchResults as $dispatchResult) {
            $routeMethods = $dispatchResult->getRoute()->getMethods();
            if (!in_array($method, $routeMethods, true) && !in_array('*', $routeMethods, true)) {
                foreach ($routeMethods as $allowedMethod) {
                    $allowedMethods[$allowedMethod] = $allowedMethod;
                }
                continue;
            }

            return $dispatchResult;
        }

        if (empty($allowedMethods)) {
            throw new RouteNotFound(sprintf('route for %s not found', $path));
        }

        if (array_key_exists('GET', $allowedMethods) && !array_key_exists('HEAD', $allowedMethods)) {
            $allowedMethods['HEAD'] = 'HEAD';
        }

        throw new MethodNotAllowed(
            array_values($allowedMethods),
            sprintf('method %s not allowed for %s path', $method, $path)
        );
    }
}
