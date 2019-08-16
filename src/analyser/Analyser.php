<?php

namespace Lxj\Review\Bot\analyser;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;

class Analyser
{
    protected $errors = [];

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function analyse(array $stmts)
    {
        return $this;
    }

    protected function assertController($stmt)
    {
        return $this->assertClassType($stmt, 'Controller');
    }

    protected function assertLogic($stmt)
    {
        return $this->assertClassType($stmt, 'BaseLogic');
    }

    protected function assertClassType($stmt, $type)
    {
        if ($stmt instanceof Class_) {
            if ($stmt->extends instanceof Name) {
                $extendNode = $stmt->extends;
                if (isset($extendNode->parts[0])) {
                    if ($extendNode->parts[0] === $type) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function assertEcho($stmt)
    {
        return $stmt instanceof Echo_;
    }

    protected function assertExit($stmt)
    {
        if ($stmt instanceof Expression) {
            if ($stmt->expr instanceof Expr\Exit_) {
                return true;
            }
        }

        return false;
    }

    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
