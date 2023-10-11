<?php

namespace Palax\Exception;



class APIException extends \RuntimeException
{
    protected array $errors;

    public function __construct(
        $message = "", $code = 0, array $errors = []
    ) {
        parent::__construct($message, $code, null);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors,
        ];
    }
}
