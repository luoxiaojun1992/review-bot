<?php

namespace Lxj\Review\Bot;

class GitBot
{
    /** @var Bot */
    protected $reviewBot;
    protected $gitConfig;
    protected $gitClient;

    public function __construct($reviewBot, $gitConfig, $gitClient = null)
    {
        $this->reviewBot = $reviewBot;
        $this->gitConfig = $gitConfig;
        $this->gitClient = $gitClient ?: \Gitlab\Client::create($this->gitConfig['api_gateway'])
            ->authenticate($this->gitConfig['access_token'], \Gitlab\Client::AUTH_URL_TOKEN);
    }

    /**
     * @param $mergeRequestUrl
     * @return array
     * @throws \Exception
     */
    public function review($mergeRequestUrl)
    {
        $urlInfo = parse_url($mergeRequestUrl);
        if (!isset($urlInfo['path'])) {
            throw new \Exception('Merge request url path not found.');
        }

        $pathArr = array_values(array_filter(explode('/', $urlInfo['path'])));
        $projectName = $pathArr[1];
        $mergeRequestId = $pathArr[3];

        $projects = $this->gitClient->projects()->all(['search' => $projectName]);

        if (count($projects) <= 0) {
            throw new \Exception('Project not found.');
        }

        if (count($projects) == 1) {
            $project = $projects[0];
            $projectId = $project['id'];

            $mergeRequest = $this->gitClient->mergeRequests()->show($projectId, $mergeRequestId);
            $sourceBranch = $mergeRequest['source_branch'];

            $storageDir = __DIR__ . '/../storage';
            $localProjectDir = $storageDir . '/' . $projectId;
            if (!is_dir($localProjectDir)) {
                $projectUrl = $project['ssh_url_to_repo'];
                shell_exec('cd ' . $storageDir . ' && git clone ' . $projectUrl . ' ./' . $projectId . ' && cd ' . $projectId . ' && git checkout ' . $sourceBranch);
            } else {
                shell_exec('cd ' . $localProjectDir . ' && git checkout ' . $sourceBranch);
            }

            $mergeRequestChanges = $this->gitClient->mergeRequests()->changes($projectId, $mergeRequestId);
            $fileChanges = $mergeRequestChanges['changes'];
            $errors = [];
            foreach ($fileChanges as $fileChange) {
                if (!$fileChange['deleted_file']) {
                    if (pathinfo($fileChange['new_path'], PATHINFO_EXTENSION) === 'php') {
                        $errors = array_merge(
                            $errors,
                            $this->reviewBot->review($localProjectDir . '/' . $fileChange['new_path'])->getErrors()
                        );
                    }
                }
            }

            return $errors;
        } else {
            throw new \Exception('Searched multiple projects:' . json_encode(array_column($projects, 'name')));
        }
    }
}