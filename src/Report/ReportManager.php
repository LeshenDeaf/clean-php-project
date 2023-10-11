<?php

namespace Palax\Report;

use Palax\App\App;

class ReportManager
{
    private string $type;
    private array $filter;

    public function __construct(string $type, array $filter)
    {
        $this->type = $type;
        $this->filter = $filter;
    }

    public function getReport(): array
    {
        if (!$className = $this->getClassName('common')) {
            return ['error' => "type {$this->type} not defined"];
        }

        return $this->generate($className);
    }

    private function getClassName(string $key): ?string
    {
        return App::getConfig(self::class)['map'][$this->type][$key] ?? null;
    }

    private function generate(string $className): array
    {
        $generator = new $className();

        if (!($generator instanceof ReportGeneratorInterface)) {
            return ['error' => "$className not instance of " . ReportGeneratorInterface::class];
        }

        return $generator->generate($this->filter);
    }

    public function getExcelReport(): array
    {
        if (!$className = $this->getClassName('excel')) {
            return ['error' => "type {$this->type} not defined"];
        }

        return $this->generate($className);
    }
}