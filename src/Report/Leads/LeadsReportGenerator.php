<?php

namespace Palax\Report\Leads;

use Palax\App\App;
use Palax\Report\ReportGeneratorInterface;

class LeadsReportGenerator implements ReportGeneratorInterface
{

    public function generate(array $filter): array
    {
        $leads = App::getBitrixLeads()->getLeads($filter);
        $users = App::getBitrixClient()->getUsers() ?? [];

        $statistics = [
            'count' => 0,

            'processed' => [
                'count' => 0,

                'successful' => [
                    'count' => 0,

                    'deal' => 0,
                    'contact' => 0,
                    'company' => 0,
                ],
                'failed' => [
                    'count' => 0,

                    'nonTargeted' => 0,
                    'phoneNumberFail' => 0,
                ],
            ],
            'notProcessed' => [
                'count' => 0,
            ],
        ];
        $config = App::getConfig(BitrixLeads::class);

        $resultByUser = [];

        $total = $statistics;

        foreach ($leads as $lead) {
            $resultByUser[$lead['ASSIGNED_BY_ID']] ??= $statistics;
            $resultByUser[$lead['ASSIGNED_BY_ID']]['user'] ??= [
                'id' => $lead['ASSIGNED_BY_ID'],
                'fio' => implode(
                    ' ',
                    array_filter([
                        $users[$lead['ASSIGNED_BY_ID']]['LAST_NAME'],
                        $users[$lead['ASSIGNED_BY_ID']]['NAME'],
                        $users[$lead['ASSIGNED_BY_ID']]['SECOND_NAME'],
                    ]) ?: [$users[$lead['ASSIGNED_BY_ID']]['EMAIL']]
                ),
            ];

            $resultByUser[$lead['ASSIGNED_BY_ID']]['count']++;
            $total['count']++;

            if ($lead['STATUS_SEMANTIC_ID'] === 'P') {
                $resultByUser[$lead['ASSIGNED_BY_ID']]['notProcessed']['count']++;
                $total['notProcessed']['count']++;

                continue;
            }

            $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['count']++;
            $total['processed']['count']++;

            if ($lead['STATUS_SEMANTIC_ID'] === 'S') {
                $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['successful']['count']++;
                $total['processed']['successful']['count']++;

                if ($lead[$config['uf']['deal']]) {
                    $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['successful']['deal']++;
                    $total['processed']['successful']['deal']++;
                }
                if ($lead[$config['uf']['contact']]) {
                    $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['successful']['contact']++;
                    $total['processed']['successful']['contact']++;
                }
                if ($lead[$config['uf']['company']]) {
                    $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['successful']['company']++;
                    $total['processed']['successful']['company']++;
                }

                continue;
            }

            $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['failed']['count']++;
            $total['processed']['failed']['count']++;

            if ($lead['STATUS_ID'] === 'JUNK') {
                $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['failed']['nonTargeted']++;
                $total['processed']['failed']['nonTargeted']++;
            } elseif ($lead['STATUS_ID'] === 'UC_ATMWXP') {
                $resultByUser[$lead['ASSIGNED_BY_ID']]['processed']['failed']['phoneNumberFail']++;
                $total['processed']['failed']['phoneNumberFail']++;
            }
        }

        return [
            'result' => array_values($resultByUser),
            'total' => $total,
        ];
    }
}