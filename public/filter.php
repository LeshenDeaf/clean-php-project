<?php

use Palax\Report\ReportManager;

require_once '../util/autoload.php';

if (!empty($_POST["date_from"]) && !empty($_POST["date_to"])) {
    $resultData = array();
    $type = $_POST["type"];
    unset($_POST["type"]);
    $arFilter = $_POST;
    $report = new ReportManager($type, $arFilter);
    $resultData = $report->getReport();

    echo json_encode($resultData);
}