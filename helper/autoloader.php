<?php

spl_autoload_register(function (string $classpath) : void {
    $classpath = str_replace('\\', '/', $classpath) . '.php';
    $classpath = str_replace('Enginr/', __DIR__ . '/../lib/', $classpath);

    require_once($classpath);
});