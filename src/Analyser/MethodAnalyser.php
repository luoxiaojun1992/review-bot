<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Stmt\ClassMethod;

class MethodAnalyser extends Analyser
{
    protected $methodLinesLimit = 500;

    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseMethodSize($stmts);

        return $this;
    }

    protected function analyseMethodSize(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                if ($stmt->getEndLine() - $stmt->getStartLine() > $this->methodLinesLimit) {
                    $this->addError($stmt->getLine(), Errors::METHOD_TOO_LARGE);
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseMethodSize($stmt->stmts);
                }
            }
        }
    }
}
