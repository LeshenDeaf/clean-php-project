<?php

namespace Palax\Result;


use LogicException;
use Palax\Exception\APIException;
use Palax\Result\Error\CriticalError;
use Palax\Result\Error\Error;
use Palax\Result\Error\LogicError;
use Stringable;

final class Result implements Stringable
{
    private bool $success;
    private $data;
    /** @var array<Error|APIException> $errors */
    private array $errors;

    public function __construct(bool $success, $data = null, array $errors = [])
    {
        $this->success = $success;
        $this->data = $data;
        $this->errors = $errors;
    }

    public static function success($data = null): Result
    {
        return new self(true, $data);
    }

    public static function errors(array $errors): Result
    {
        return new self(false, null, $errors);
    }

    public static function critical(?string $error = null): Result
    {
        return new self(false, null, [
            new CriticalError($error)
        ]);
    }

    public static function illogical(?string $error = null): Result
    {
        return new self(false, null, [
            new LogicError($error)
        ]);
    }

    public function errored(): bool
    {
        return count($this->getErrors()) !== 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function getData(?string $key = null, $default = null)
    {
        if (!$key) {
            return $this->data;
        }

        if (!is_array($this->data)) {
            throw new LogicException('Cannot get data by key from non-array');
        }

        return $this->data[$key] ?? $default;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'errors' => array_map(static fn($error) => $error->toArray(), $this->errors),
            'data' => $this->data
        ];
    }

}
