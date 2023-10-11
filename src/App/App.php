<?php

namespace Palax\App;


use Palax\Exception\APIException;
use Palax\Result\Result;
use Throwable;
use function array_filter;

class App
{
    private static ?Response $response = null;
    private static ?Request $request = null;
    private static ?array $config = null;

    public function __construct()
    {
        if (!self::$response) {
            self::$response = new Response();
        }
        if (!self::$request) {
            self::$request = new Request();
        }
    }

    public static function getResponse(): Response
    {
        if (!self::$response) {
            self::$response = new Response();

            return self::$response;
        }

        return self::$response;
    }

    public static function getRequest(): Request
    {
        if (!self::$request) {
            self::$request = new Request();

            return self::$request;
        }

        return self::$request;
    }

    public function work(): void
    {
        try {
            $result = Router::route();
            self::$response->setBody($result);

            if ($result instanceof Result && $result->errored()) {
                $code = array_filter($result->getErrors(), static fn($el) => $el instanceof APIException) ? 500 : 400;
                self::$response->code($code)->return();
                return;
            }

            self::$response->return();
        } catch (APIException $exception) {
            self::$response
                ->setBody(Result::errors([$exception]))
                ->code($exception->getCode())
                ->return();
            return;
        } catch (Throwable $exception) {
            self::$response
                ->setBody(Result::critical('Необработанная ошибка сервера'))
                ->code(500)
                ->return();
            return;
        }
    }

    public static function getConfig(?string $className = null): array
    {
        self::$config ??= require_once __DIR__ . '/../../config/main.php';

        return $className ? self::$config[$className] : self::$config;
    }
}
