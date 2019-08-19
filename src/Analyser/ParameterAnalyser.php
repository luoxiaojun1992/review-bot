<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Stmt\ClassMethod;

class ParameterAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseUse($stmts);

        return $this;
    }

    protected function analyseUse(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                foreach ($stmt->params as $i => $param) {
                    if (!is_null($param->default)) {
                        if (isset($stmt->params[$i + 1])) {
                            if (is_null($stmt->params[$i + 1]->default)) {
                                $this->addError([
                                    'file' => $this->filePath,
                                    'line' => $stmt->getLine(),
                                    'code' => Errors::ME_ARGS_WITH_DEFAULT_VALUE,
                                    'msg' => 'Method arguments with default values MUST go at the end of the argument list',
                                ]);
                                break;
                            }
                        }
                    }
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseUse($stmt->stmts);
                }
            }
        }
    }
}
