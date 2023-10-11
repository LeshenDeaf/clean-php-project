<?php

namespace Palax\Bitrix\Leads;

use Palax\App\App;

class BitrixLeads
{
    public const CLOSED_STAGES = ['F', 'S'];

    public function getLeads(array $arFilter): array
    {
        $filter = [
            'ASSIGNED_BY_ID' => $arFilter['users'] ?? null,
            '>=DATE_CREATE' => $arFilter['date_from'] . ' 00:00:00',
            '<=DATE_CREATE' => $arFilter['date_to'] . ' 23:59:59',
            '>=DATE_CLOSED' => [$arFilter['date_from'] . ' 00:00:00', false],
            '<=DATE_CLOSED' => [$arFilter['date_to'] . ' 23:59:59', false],
        ];

        $config = App::getConfig(self::class);

        $cachedLeads = $this->getCachedLeads($filter);

        if ($cachedLeads) {
            array_walk($cachedLeads, static function (&$call) use ($config) {
                $call = [
                    'ID' => $call['id'],
                    'ASSIGNED_BY_ID' => $call['assigned_by_id'],
                    'STATUS_ID' => $call['status_id'],
                    'DATE_CREATE' => $call['date_create'],
                    'DATE_CLOSED' => $call['date_closed'],
                    'TITLE' => $call['title'],
                    $config['uf']['deal'] => $call['deal'],
                    $config['uf']['contact'] => $call['contact'],
                    $config['uf']['company'] => $call['company'],
                ];
            });

            $filter['!=ID'] = array_column($cachedLeads, 'ID');
        }

        $leads = App::getBitrixClient()->getListReverse(
            'crm.lead.list',
            [
                "filter" => $filter,
                "select" => ['*', 'UF_*', 'ASSIGNED_BY_ID', 'DATE_CREATE', 'DATE_CLOSED', 'STATUS_SEMANTIC_ID'],
                'start' => -1
            ],
            [$this, 'saveLeads']
        );

        return array_merge($cachedLeads, $leads);
    }

    public function getCachedLeads(array $filter): array
    {
        // "select * from calls where call_start_date >= {$filter['>=CALL_START_DATE']} and call_start_date <= {$filter['<=CALL_START_DATE']} order by call_start_date asc"

        return (new Query())
            ->from('processed_leads')
            ->columns([
                'id',
                'assigned_by_id',
                'date_create',
                'date_closed',
                'status_id',
                'title',
                'deal',
                'contact',
                'company',
            ])
            ->where(['>=', 'date_create', $filter['>=DATE_CREATE']])
            ->andWhere(['<=', 'date_create', $filter['<=DATE_CREATE']])
            ->andWhere(['>=', 'date_closed', $filter['>=DATE_CLOSED']])
            ->andWhere(['<=', 'date_closed', $filter['<=DATE_CLOSED']])
            ->andWhere(['assigned_by_id' => $filter['ASSIGNED_BY_ID']])
            ->orderBy('id')
            ->fetchAll() ?: [];
    }

    public function saveLeads(array $leads): void
    {
        $config = App::getConfig(self::class);
        $command = new Command();

        pg_query("BEGIN") or die("Could not start transaction\n");
        try {
            foreach ($leads as $lead) {
                if (!in_array($lead['STATUS_SEMANTIC_ID'], self::CLOSED_STAGES) || !$lead['DATE_CLOSED']) {
                    continue;
                }

                $createdAt = explode(' ', str_replace(['T', '+'], ' ', $lead['DATE_CREATE']));
                $closedAt = explode(' ', str_replace(['T', '+'], ' ', $lead['DATE_CLOSED']));

                $res = $command->insert('processed_leads', [
                    'id' => (int)$lead['ID'],
                    'assigned_by_id' => (int)$lead['ASSIGNED_BY_ID'],
                    'date_create' => $createdAt[0] . ' ' . $createdAt[1],
                    'date_closed' => $closedAt[0] . ' ' . $closedAt[1],
                    'status_id' => $lead['STATUS_ID'],
                    'title' => $lead['TITLE'],
                    'deal' => (bool)($lead[$config['uf']['deal']] ?? false),
                    'contact' => (bool)($lead[$config['uf']['contact']] ?? false),
                    'company' => (bool)($lead[$config['uf']['company']] ?? false),
                ]);
                if (!$res[0]) {
                    pg_query("ROLLBACK") or die("Transaction rollback failed\n");

//                    echo $res[1];
                }
            }

            pg_query("COMMIT") or die("Transaction commit failed\n");
        } catch (\Throwable $throwable) {
            pg_query("ROLLBACK") or die("Transaction rollback failed\n");
        }
    }
}