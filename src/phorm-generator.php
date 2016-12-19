<?php

chdir(__DIR__ . '/../tests/fixture');

global $config;

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'PhormGenerator' => __DIR__ . '/PhormGenerator/',
],true)->register();

$console = new \PhormGenerator\Console;
try {
    $console->handle([
        'task' => 'phorm',
        'action' => 'index',
    ]);
}
catch(\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString(), PHP_EOL;
}
