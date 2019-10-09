<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Stmt\ClassMethod;

class MethodAnalyser extends Analyser
{
    protected $methodLinesLimit = 500;

    public function analyse(array $stmts)
    {
        $this->analyseMethodSize($stmts);

        return $this;
    }

    protected function analyseMethodSize(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($element instanceof ClassMethod) {
                if ($element->getEndLine() - $element->getStartLine() > $this->methodLinesLimit) {
                    $this->addError($element->getLine(), Errors::METHOD_TOO_LARGE);
                }
            }
        });
    }
}
