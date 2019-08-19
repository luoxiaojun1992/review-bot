<?php

require_once __DIR__ . '/vendor/autoload.php';

$path = $argv[1];

echo json_encode((new \Lxj\Review\Bot\Bot(require __DIR__ . '/config/analyser.php'))->review($path)->getErrors(), JSON_PRETTY_PRINT);
echo PHP_EOL;
