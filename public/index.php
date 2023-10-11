<?php

//use Palax\TableInitializer;
//
//require_once 'util/autoload.php';
//
//TableInitializer::ShowHead();
//TableInitializer::showEnd();

use Palax\App\App;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once '../util/autoload.php';
require_once '../config/routes.php';

$app = new App();
$app->work();

