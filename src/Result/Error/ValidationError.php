<?php

namespace Palax\Result\Error;

final class ValidationError extends Error
{
    public const TYPE = 'validation';
    private string $attribute;

    public function __construct(
        string $message,
        string $attribute
    )
    {
        parent::__construct($message);
        $this->attribute = $attribute;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    protected function _toArray(): array
    {
        return [
            'attribute' => $this->attribute
        ];
    }
}
