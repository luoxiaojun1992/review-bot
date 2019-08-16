<?php

namespace Lxj\Review\Bot;

class Bot
{
    protected $errors = [];

    protected $analysers = [];

    public function __construct()
    {
        $this->analysers = require __DIR__ . '/../config/analyser.php';
    }

    public function review($fileOrDir)
    {
        if (is_dir($fileOrDir)) {
            $fd = opendir($fileOrDir);
            while ($subFileOrDir = readdir($fd)) {
                if (!in_array($subFileOrDir, ['.', '..'])) {
                    $this->review($fileOrDir . '/' . $subFileOrDir);
                }
            }
            closedir($fd);
        } else {
            $ast = $this->parseAst($fileOrDir);
            if (is_array($ast) && count($ast) > 0) {
                $this->analyse($fileOrDir, $ast);
            }
        }

        return $this;
    }

    protected function parseAst($filePath)
    {
        $parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
        return $parser->parse(file_get_contents($filePath));
    }

    protected function analyse($filePath, $ast)
    {
        foreach ($this->analysers as $analyserClass) {
            /** @var \Lxj\Review\Bot\analyser\Analyser $analyser */
            $analyser = (new $analyserClass($filePath));
            $this->errors = array_merge($this->errors, $analyser->analyse($ast)->getErrors());
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
