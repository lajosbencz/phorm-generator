<?php

global $di, $config;

$config = new Phalcon\Config([
    'phalcon' => [
        'modelsDir' => __DIR__ . '/models/',
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'phorm_test',
        'username' => 'phorm_test',
        'password' => 'DGVtmTRTcU1GPbX1',
    ],
    'phorm-generator' => [
        'namespace' => 'PhormTest\Models\\',
        'directory' => __DIR__ . '/models/',
        'generated' => 'Auto',
        'baseModel' => 'Phalcon\Mvc\Model',
        'baseView' => 'Phalcon\Mvc\View',
    ],
]);

return $config;
