<?php

namespace Palax\Result\Error;

final class CriticalError  extends Error
{
    public const TYPE = 'critical';

    public function getType(): string
    {
        return self::TYPE;
    }

    protected function _toArray(): array
    {
        return [];
    }
}
