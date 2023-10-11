<?php

namespace Palax\App;


use Palax\Result\Result;
use Stringable;
use function http_response_code;

class Response
{
    private int $code = 200;
    private array $headers = [];
    private null|string|Stringable $result = null;


    public function return(): void
    {
        http_response_code($this->code);

        foreach ($this->headers as $header => $isUsed) {
            $isUsed && header($header);
        }

        echo $this->result;
    }

    public function code(int $code = 200): Response
    {
        $this->code = $code;

        return $this;
    }

    public function json(): Response
    {
        $this->setHeader('Content-Type: application/json');

        return $this;
    }

    public function setHeader(string $header): Response
    {
        $this->headers[$header] = true;

        return $this;
    }

    public function removeHeader(string $header): Response
    {
        unset($this->headers[$header]);

        return $this;
    }

    public function getResult(): null|string|Stringable
    {
        return $this->result;
    }

    public function setBody(string|Stringable $body): Response
    {
        $this->result = $body;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

}
