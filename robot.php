<?php

require_once __DIR__ . '/vendor/autoload.php';

$path = $argv[1];

echo json_encode((new \Lxj\Review\Bot\Bot())->review($path)->getErrors());
echo PHP_EOL;
