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

    public function reviewByCode($filePath, $code)
    {
        $ast = $this->parseAst($code);
        if (is_array($ast) && count($ast) > 0) {
            $this->analyse($filePath, $ast);
        }
    }

    public function review($fileOrDir)
    {
        if (is_dir($fileOrDir)) {
            $fd = opendir($fileOrDir);
            while ($subFileOrDir = readdir($fd)) {
                if (!in_array($subFileOrDir, ['.', '..'])) {
                    $filePath = $fileOrDir . '/' . $subFileOrDir;
                    $this->reviewByCode($fileOrDir, file_get_contents($filePath));
                }
            }
            closedir($fd);
        } else {
            $this->reviewByCode($fileOrDir, file_get_contents($fileOrDir));
        }

        return $this;
    }

    protected function parseAst($code)
    {
        return $this->parser->parse($code);
    }

    protected function analyse($filePath, $ast)
    {
        foreach ($this->analysers as $analyserClass) {
            if (is_array($analyserClass)) {
                list($analyserClass, $analyserOptions) = $analyserClass;
            } else {
                $analyserOptions = [];
            }

            /** @var \Lxj\Review\Bot\Analyser\Analyser $analyser */
            $analyser = (new $analyserClass($filePath, $this->ignored, $analyserOptions));
            $this->collectErrors($analyser->analyse($ast)->getErrors());
        }
    }
}
