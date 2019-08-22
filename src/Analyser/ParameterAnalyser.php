<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Stmt\ClassMethod;

class ParameterAnalyser extends Analyser
{
    protected $argumentLengthLimit = 10;

    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseParameter($stmts);

        return $this;
    }

    protected function analyseParameter(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                if (count($stmt->params) > $this->argumentLengthLimit) {
                    $this->addError($stmt->getLine(), Errors::TOO_MANY_ARGUMENTS);
                }

                foreach ($stmt->params as $i => $param) {
                    if (!is_null($param->default)) {
                        if (isset($stmt->params[$i + 1])) {
                            if (is_null($stmt->params[$i + 1]->default)) {
                                $this->addError($stmt->getLine(), Errors::ME_ARGS_WITH_DEFAULT_VALUE);
                                break;
                            }
                        }
                    }
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseParameter($stmt->stmts);
                }
            }
        }
    }
}
