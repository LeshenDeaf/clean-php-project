<?php

namespace Palax\Bitrix;

use CRest\CRest;

class BitrixClient
{
    private static ?array $users = [];

    public function getDepartments(): ?array
    {
        return $this->get('department.get', []);
    }

    public function get(string $method, array $params)
    {
        return CRest::call($method, $params)['result'] ?? null;
    }

    public function resetUsers(): ?array
    {
        self::$users = null;

        return $this->getUsers();
    }

    public function getUsers(): ?array
    {
        if (!self::$users) {
            $params = ['FILTER' => ["ACTIVE" => true]];

            self::$users ??= [];
            $params['FILTER']['>ID'] = null;
            while ($newResult = $this->get('user.get', $params) ?? []) {
                self::$users += array_column($newResult, null, 'ID');

                $params['FILTER']['>ID'] = $newResult[count($newResult) - 1]['ID'] ?? 0;

                if (!$params['FILTER']['>ID']) {
                    break;
                }

                usleep(200000);
            }
        }

        return self::$users;
    }

    public function getList(string $method, array $params, ?callable $beforePause = null): array
    {
        return $this->getAllElements('<ID', $method, $params, $beforePause);
    }

    public function getListReverse(string $method, array $params, ?callable $beforePause = null): array
    {
        return $this->getAllElements('>ID', $method, $params, $beforePause);
    }

    public function getAllElements(string $filterParam, string $method, array $params, ?callable $beforePause = null, string $filterName = 'filter'): array
    {
        $output = [];

        $params[$filterName][$filterParam] = null;

        while ($newResult = $this->get($method, $params) ?? []) {
            $output = array_merge($output, $newResult);
            $params[$filterName][$filterParam] = $newResult[count($newResult) - 1]['ID'] ?? 0;

            if (!$params[$filterName][$filterParam]) {
                break;
            }

            $beforePause && $beforePause($newResult);

            usleep(200000);
        }

        return $output;
    }
}