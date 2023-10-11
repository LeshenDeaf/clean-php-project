<?php

namespace Palax\App;

use function array_keys;
use function array_map;
use function compact;
use function explode;
use function filter_input;
use const FILTER_SANITIZE_SPECIAL_CHARS;
use const INPUT_GET;

class Request
{
    private string $url;
    private array $headers;
    private array $query;
    private array $body;
    private string $method;

    public function __construct()
    {
        $this->url = explode('?', $_SERVER['REQUEST_URI'])[0] ?? '/';
        $this->headers = getallheaders();
        $this->query = array_combine(
            array_keys($_GET),
            array_map(fn($name) => $this->filter($name), array_keys($_GET))
        );
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function filter(string $name, int $filter = FILTER_SANITIZE_SPECIAL_CHARS)
    {
        return filter_input(INPUT_GET, $name, $filter);
    }

    public function toArray(): array
    {
        extract(get_object_vars($this));
        return compact('url', 'headers', 'query', 'body');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getQuery(string $key = null)
    {
        return $key ? $this->query[$key] ?? null : $this->query;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}