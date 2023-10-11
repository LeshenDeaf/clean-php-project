<?php

use Palax\App\Router;
use Palax\Bitrix\Leads\BitrixLeads;
use Palax\TableInitializer;
use Palax\Report\Leads\LeadsReportGenerator;
use Palax\Report\ReportManager;

return [
    ReportManager::class => [
        'map' => [
            'leads_report' => [
                'common' => LeadsReportGenerator::class,
            ]
        ],
    ],
    TableInitializer::class => [
        'prefix' => '/b24/reports',
        'routes' => [
            [
                'route' => '/index.php',
                'label' => 'На главную'
            ],
            [
                'route' => '/employee_load.php',
                'label' => 'Нагрузка по сотрудникам'
            ],
            [
                'route' => '/call_duration.php',
                'label' => 'Длительность разговора'
            ],
            [
                'route' => '/average_call_time.php',
                'label' => 'Среднее время звонка'
            ],
            [
                'route' => '/leads_report.php',
                'label' => 'Отчёт по лидам'
            ],
        ]
    ],
    Router::class => [
        'prefix' => '/leads-report',
    ],
    BitrixLeads::class => [
        'statuses' => [
            'successful' => ['CONVERTED'],
            'failed' => ['JUNK', 'UC_ATMWXP'],
        ],

        'uf' => [
            'deal' => 'UF_CRM_1696418110',
            'contact' => 'UF_CRM_1696418119',
            'company' => 'UF_CRM_169641813',
        ]
    ],
    'excel' => '/b24/reports/excel.php',
    'filter' => '/b24/reports/filter.php',
];