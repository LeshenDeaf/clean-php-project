<?php

namespace Palax\Handler;

use Stringable;

interface HandlerInterface
{
    public function run(): string|Stringable;
}