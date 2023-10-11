<?php

namespace Palax\Handler;

use Palax\Handler\HandlerInterface;
use Palax\Result\Result;
use Stringable;

class HealthCheck implements HandlerInterface
{

    public function run(): string|Stringable
    {
        return Result::success('hello');
    }
}