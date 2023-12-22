# ddrv/router

## Install

```shell
composer require ddrv/router:^1.0
```

## Usage

```php
/**
 * \Ddrv\Router\Router is a union of:
 * - Ddrv\Router\Entity\RouteRegister
 * - Ddrv\Router\Entity\RouteDispatcher
 * - Ddrv\Router\Entity\UriGenerator
 */
$router = new \Ddrv\Router\Router();

// Register routes
$router->get('/hello/{name}', 'getHelloHandler')->name('hello')->where('name', '[a-z]+')->middleware('HttpCache');

$router->group('/api', function (\Ddrv\Router\Contract\RouteGroupInterface $group) {
    $group->get('/resources', 'ApiResourceListHandler')
    $group->post('/resources', 'ApiResourceCreateHandler')
    $group->options('/resources', 'ApiResourceOptionsHandler')
    $group->group('/resources', function (\Ddrv\Router\Contract\RouteGroupInterface $group) {
        $group->get('/{id}', 'ApiResourceShowHandler')->where('id', '\d+')
        $group->put('/{id}', 'ApiResourceReplaceHandler')->where('id', '\d+')
        $group->patch('/{id}', 'ApiResourceModifyHandler')->where('id', '\d+')
        $group->delete('/{id}', 'ApiResourceDeleteHandler')->where('id', '\d+')
        $group->options('/{id}', 'ApiResourceOptionsHandler')->where('id', '\d+')
    });
})->middleware('ApiAuthMiddleware');

// Dispatch
$result = $router->dispatch('GET', '/hello/world');
$result->getPathParameters();               // ['name' => 'world']
$result->getRoute()->getHandler();          // 'getHelloHandler'
$result->getRoute()->getMethods();          // ['GET']
$result->getRoute()->getPattern();          // '/hello/{name}'
$result->getRoute()->getName();             // 'hello'
$result->getRoute()->getParameterRegexps(); // ['name' => '[a-z]+']
$result->getRoute()->getMiddlewares();      // iterator with one element 'HttpCache'

try {
    $router->dispatch('POST', '/hello/world');
} catch (\Ddrv\Router\Exception\MethodNotAllowed $exception) {
    $exception->getAllowedMethods(); // ['GET']
}

try {
    $router->dispatch('GET', '/');
} catch (\Ddrv\Router\Exception\RouteNotFound $exception) {
}

// URI generation
$router->uri('hello', ['name' => 'world'], ['from' => 'me'], 'https://dev.io'); // 'https://dev.io/hello/world?from=me'
$router->uri('hello', ['name' => 'world'], ['from' => 'me']);                   // '/hello/world?from=me'
$router->uri('hello', ['name' => 'world']);                                     // '/hello/world'

try {
    $router->uri('hello');
} catch (\Ddrv\Router\Exception\ParametersRequired $exception) {
    $exception->getRequiredParameters(); // ['name']
}

try {
    $router->uri('home');
} catch (\Ddrv\Router\Exception\UndefinedRoute $exception) {
}
```
