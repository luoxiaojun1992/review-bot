<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Invalid arguments.';
    echo PHP_EOL;
    exit(1);
}

$mergeRequestUrl = $argv[1];

echo json_encode((new \Lxj\Review\Bot\GitBot(
    new \Lxj\Review\Bot\Bot(require __DIR__ . '/config/analyser.php'),
    require __DIR__ . '/config/gitlab.php')
)->review($mergeRequestUrl)->getErrors(), JSON_PRETTY_PRINT);
