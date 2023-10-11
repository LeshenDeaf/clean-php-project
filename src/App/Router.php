<?php

namespace Palax\App;


use Palax\Exception\APIException;
use Palax\Handler\HandlerInterface;
use Palax\Result\Result;
use Stringable;
use function class_exists;
use function rtrim;
use function str_replace;
use function strtolower;
use function trim;

class Router
{
    /**
     * @var array $handlers = [string => [RequestMethod::case->value => Handler::class,]]
     */
    public static array $handlers;


    public static function route(): string|Stringable
    {
        $handler = self::getHandler();

        return $handler->run();
    }

    public static function add(string $url, RequestMethod $method, string $handler): void
    {
        if (!trim($handler)) {
            throw new APIException(
                'No handler passed',
                500,
                ['error' => "No handler determined for root $url"]
            );
        }
        self::$handlers[rtrim(trim($url), '/')][$method->value] = $handler;
    }

    private static function getHandler(): HandlerInterface
    {
        $route = rtrim(trim(App::getRequest()->getUrl()), "/");
        $method = strtolower(App::getRequest()->getMethod());

        $route = str_replace(App::getConfig(self::class)['prefix'] . '/', '', $route);

        $methods = self::$handlers[$route] ?? null;
        if (!$methods) {
            throw new APIException(
                "Unknown route '{$route}'",
                404, ['error' => 'Route not found']
            );
        }

        $class = $methods[$method] ?? null;
        if (!$class) {
            throw new APIException(
                "Method '$method' is not defined for route '$route'",
                405, [
                    'error' => 'Attempt to call method of unknown class'
                ]
            );
        }

        if (!class_exists($class)) {
            throw new APIException(
                "Unknown class '{$class}'",
                500, [
                    'error' => 'Attempt to call method of unknown class'
                ]
            );
        }

        return new $class();
    }
}
