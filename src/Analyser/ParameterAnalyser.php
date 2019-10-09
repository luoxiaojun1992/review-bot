<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Stmt\ClassMethod;

class ParameterAnalyser extends Analyser
{
    protected $argumentLengthLimit = 10;

    public function analyse(array $stmts)
    {
        $this->analyseParameter($stmts);

        return $this;
    }

    protected function analyseParameter(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($element instanceof ClassMethod) {
                if (count($element->params) > $this->argumentLengthLimit) {
                    $this->addError($element->getLine(), Errors::TOO_MANY_ARGUMENTS);
                }

                foreach ($element->params as $i => $param) {
                    if (!is_null($param->default)) {
                        if (isset($element->params[$i + 1])) {
                            if (is_null($element->params[$i + 1]->default)) {
                                $this->addError($element->getLine(), Errors::ME_ARGS_WITH_DEFAULT_VALUE);
                                break;
                            }
                        }
                    }
                }
            }
        });
    }
}
