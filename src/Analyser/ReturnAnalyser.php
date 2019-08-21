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
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseReturnValue($stmts);

        return $this;
    }

    protected function analyseReturnValue(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Return_) {
                if ($stmt->expr instanceof Array_) {
                    $returnArrKeys = [];
                    foreach ($stmt->expr->items as $arrayItem) {
                        if ($arrayItem->key instanceof String_) {
                            $returnArrKeys[] = $arrayItem->key->value;
                        }
                    }
                    if (count(array_intersect(['code', 'msg'], $returnArrKeys)) == 2 ||
                        count(array_intersect(['code', 'message'], $returnArrKeys)) == 2
                    ) {
                        if ($this->isLogic) {
                            $this->addError($stmt->getLine(), Errors::RET_API_FORMAT_DATA_IN_LOGIC);
                        }
                    }
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseReturnValue($stmt->stmts);
                }
            }
        }
    }
}
