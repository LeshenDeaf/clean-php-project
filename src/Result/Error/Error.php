<?php

namespace Palax\Result\Error;

abstract class Error
{
    private string $message;
    public function __construct(
        string $message
    )
    {
        $this->message = $message;
    }

    public function toArray(): array
    {
        return [
                'type' => $this->getType(),
                'message' => $this->getMessage()
            ] + $this->_toArray();
    }

    abstract public function getType(): string;

    public function getMessage(): string
    {
        return $this->message;
    }

    abstract protected function _toArray(): array;
}
