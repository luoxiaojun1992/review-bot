<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Expr\FuncCall;

class ExprAnalyser extends Analyser
{
    const UNKNOWN_VALUE_TYPE = 'unknown';

    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseInArrayExpr($stmts);

        return $this;
    }

    protected function analyseInArrayExpr(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->analyseFunCall($stmt)) {
                $this->addError($stmt->getLine(), Errors::INCONSISTENT_IN_ARRAY_ARG_TYPE);
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseInArrayExpr($stmt->stmts);
                }
            }
        }
    }

    protected function analyseFunCall($obj)
    {
        foreach ($obj as $property) {
            if (is_object($property)) {
                if ($property instanceof FuncCall) {
                    if (strtolower($property->name->getFirst()) === 'in_array') {
                        $args = $property->args;
                        list($needle, $haystack) = $args;

                        $needleType = $this->getValueType($needle->value);
                        if ($needleType === self::UNKNOWN_VALUE_TYPE) {
                            return false;
                        }

                        $haystackItems = $haystack->value->items;
                        foreach ($haystackItems as $haystackItem) {
                            $haystackItemType = $this->getValueType($haystackItem->value);
                            if (($haystackItemType !== self::UNKNOWN_VALUE_TYPE) && ($haystackItemType !== $needleType)) {
                                return true;
                            }
                        }
                    }
                } else {
                    if ($this->analyseFunCall($property)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function getValueType($value)
    {
        if (isset($value->value)) {
            $valueType = gettype($value->value);
        } elseif (isset($value->name)) {
            $name = strtolower($value->name->getFirst());
            if ($name === 'null') {
                $valueType = 'null';
            } elseif ($name === 'true') {
                $valueType = 'bool';
            } elseif ($name === 'false') {
                $valueType = 'bool';
            } else {
                $valueType = self::UNKNOWN_VALUE_TYPE;
            }
        } elseif (isset($value->items)) {
            $valueType = 'array';
        } elseif (isset($needleValue->class)) {
            $valueType = 'object';
        } else {
            $valueType = self::UNKNOWN_VALUE_TYPE;
        }

        return $valueType;
    }
}
