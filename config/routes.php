<?php

use Palax\App\RequestMethod;
use Palax\App\Router;
use Palax\Handler\HealthCheck;


Router::add('health-check', RequestMethod::Get, HealthCheck::class);
