<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Invalid arguments.';
    echo PHP_EOL;
    exit(1);
}

$mergeRequestUrl = $argv[1];
$urlInfo = parse_url($mergeRequestUrl);
if (!isset($urlInfo['path'])) {
    echo 'Merge request url path not found.';
    echo PHP_EOL;
    exit(1);
}

$pathArr = array_values(array_filter(explode('/', $urlInfo['path'])));
$projectName = $pathArr[1];
$mergeRequestId = $pathArr[3];

$gitlabConfig = require __DIR__ . '/config/gitlab.php';
$client = \Gitlab\Client::create($gitlabConfig['api_gateway'])
    ->authenticate($gitlabConfig['access_token'], \Gitlab\Client::AUTH_URL_TOKEN)
;

$projects = $client->projects()->all(['search' => $projectName]);

if (count($projects) == 1) {
    $project = $projects[0];
    $projectId = $project['id'];

    $mergeRequest = $client->mergeRequests()->show($projectId, $mergeRequestId);
    $sourceBranch = $mergeRequest['source_branch'];

    $storageDir = __DIR__ . '/storage';
    $localProjectDir = $storageDir . '/' . $projectId;
    if (!is_dir($localProjectDir)) {
        $projectUrl = $project['ssh_url_to_repo'];
        shell_exec('cd ' . $storageDir . ' && git clone ' . $projectUrl . ' ./' . $projectId . ' && cd ' . $projectId . ' && git checkout ' . $sourceBranch);
    } else {
        shell_exec('cd ' . $localProjectDir . ' && git checkout ' . $sourceBranch);
    }

    $mergeRequestChanges = $client->mergeRequests()->changes($projectId, $mergeRequestId);
    $fileChanges = $mergeRequestChanges['changes'];
    $reviewBot = (new \Lxj\Review\Bot\Bot(require __DIR__ . '/config/analyser.php'));
    $errors = [];
    foreach ($fileChanges as $fileChange) {
        if (!$fileChange['deleted_file']) {
            $errors = array_merge($errors, $reviewBot->review($localProjectDir . '/' . $fileChange['new_path'])->getErrors());
        }
    }

    echo json_encode($errors, JSON_PRETTY_PRINT);
    echo PHP_EOL;
} else {
    echo 'Searched multiple projects:';
    echo PHP_EOL;
    echo json_encode(array_column($projects, 'name'), JSON_PRETTY_PRINT);
    echo PHP_EOL;
}
