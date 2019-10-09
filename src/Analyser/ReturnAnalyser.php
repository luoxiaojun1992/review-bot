<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;

class ReturnAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        $this->analyseClassTypes($stmts);
        $this->analyseReturnValue($stmts);

        return $this;
    }

    protected function analyseReturnValue(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($element instanceof Return_) {
                if ($element->expr instanceof Array_) {
                    $returnArrKeys = [];
                    foreach ($element->expr->items as $arrayItem) {
                        if ($arrayItem->key instanceof String_) {
                            $returnArrKeys[] = $arrayItem->key->value;
                        }
                    }
                    if (count(array_intersect(['code', 'msg'], $returnArrKeys)) == 2 ||
                        count(array_intersect(['code', 'message'], $returnArrKeys)) == 2
                    ) {
                        if ($this->isLogic) {
                            $this->addError($element->getLine(), Errors::RET_API_FORMAT_DATA_IN_LOGIC);
                        }
                    }
                }
            }
        });
    }
}
