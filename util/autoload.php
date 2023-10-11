<?php

spl_autoload_register(function ($className) {
    $className = str_replace("\\", "/", $className);

    if (stripos($className, 'Palax') !== false) {
        $className = str_replace('Palax/', '', $className);
        require_once "../src/$className.php";

        return;
    }

    require_once '../' . $className . '.php';
});