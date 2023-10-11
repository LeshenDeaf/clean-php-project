<?php

namespace Palax\Report;

interface ReportGeneratorInterface
{
    public function generate(array $filter): array;
}