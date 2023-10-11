<?php

use Palax\Report\ReportManager;

require_once '../util/autoload.php';

if (!empty($_POST["date_from"]) && !empty($_POST["date_to"])) {
    $resultData = array();
    $type = $_POST["type"];
    unset($_POST["type"]);
    $arFilter = $_POST;
    $report = new ReportManager($type, $arFilter);
    $resultData = $report->getExcelReport();
    Header("Content-Type: application/force-download");
    Header("Content-Type: application/octet-stream");
    Header("Content-Type: application/download");
    Header("Content-Disposition: attachment;filename=report_" . $type . ".xls");
    Header("Content-Transfer-Encoding: binary");
    ?>
    <html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            td {
                mso-number-format: \@;
            }

            .number0 {
                mso-number-format: 0;
            }

            .number2 {
                mso-number-format: Fixed;
            }

            th {
                border: 2px solid #dee2e6;
            }
        </style>
    </head>
    <body>
    <?= $resultData['html'] ?>
    </body>
    </html>
    <?php
} ?><?php
