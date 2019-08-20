<?php

namespace Lxj\Review\Bot;

use Lxj\Review\Bot\Traits\ErrorCollector;

class Bot
{
    use ErrorCollector;

    protected $analysers = [];
    protected $parser;
    protected $ignored;

    public function __construct($analysers, $ignored = [], $parser = null)
    {
        $this->analysers = $analysers;
        $this->ignored = $ignored;
        $this->parser = $parser ?: (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
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
        return $this->parser->parse(file_get_contents($filePath));
    }

    protected function analyse($filePath, $ast)
    {
        foreach ($this->analysers as $analyserClass) {
            /** @var \Lxj\Review\Bot\Analyser\Analyser $analyser */
            $analyser = (new $analyserClass($filePath, $this->ignored));
            $this->collectErrors($analyser->analyse($ast)->getErrors());
        }
    }
}
