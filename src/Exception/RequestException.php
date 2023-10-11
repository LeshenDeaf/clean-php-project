<?php

namespace Palax\Exception;


use Palax\App\Request;

class RequestException extends APIException
{
    protected ?Request $request;

    public function __construct(
        $message = "", ?Request $request = null, $errors = []
    ) {
        parent::__construct($message, 400, $errors);
        $this->request = $request;
        $this->errors = $errors;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest($request): void
    {
        $this->request = $request;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors,
            'request' => $this->request->toArray(),
        ];
    }
}
