<?php

namespace Palax\Result\Error;

final class LogicError extends Error
{
    public const TYPE = 'logic';

    public function getType(): string
    {
        return self::TYPE;
    }

    protected function _toArray(): array
    {
        return [];
    }
}
