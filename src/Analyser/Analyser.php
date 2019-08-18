<?php

namespace Lxj\Review\Bot\Analyser;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class Analyser
{
    protected $errors = [];

    protected $filePath;

    protected $isController = false;
    protected $isLogic = false;
    protected $isCommand = false;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function analyse(array $stmts)
    {
        return $this;
    }

    protected function analyseClassTypes(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertController($stmt)) {
                $this->isController = true;
            }
            if ($this->assertLogic($stmt)) {
                $this->isLogic = true;
            }
            if ($this->assertCommand($stmt)) {
                $this->isCommand = true;
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseClassTypes($stmt->stmts);
                }
            }
        }
    }

    protected function assertController($stmt)
    {
        if ($this->assertClassExtends($stmt, 'Controller')) {
            return true;
        }

        if ($stmt instanceof Namespace_) {
            if (count(array_intersect(['App', 'Http', 'Controllers'], $stmt->name->parts)) >= 3) {
                return true;
            }
        }

        return false;
    }

    protected function assertLogic($stmt)
    {
        if ($this->assertClassExtends($stmt, 'BaseLogic')) {
            return true;
        }

        if ($stmt instanceof Namespace_) {
            if (count(array_intersect(['App', 'Domains', 'Logics'], $stmt->name->parts)) >= 3) {
                return true;
            }
        }

        return false;
    }

    protected function assertCommand($stmt)
    {
        if ($this->assertClassExtends($stmt, 'Command')) {
            return true;
        }

        if ($stmt instanceof Namespace_) {
            if (count(array_intersect(['App', 'Console', 'Commands'], $stmt->name->parts)) >= 3) {
                return true;
            }
        }

        return false;
    }

    protected function assertClassExtends($stmt, $base)
    {
        if ($stmt instanceof Class_) {
            if ($stmt->extends instanceof Name) {
                $extendNode = $stmt->extends;
                if (isset($extendNode->parts[0])) {
                    if ($extendNode->parts[0] === $base) {
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

    protected function assertPublicMethod($stmt)
    {
        if ($stmt instanceof ClassMethod) {
            return $stmt->isPublic();
        }

        return false;
    }

    protected function assertMethodWithoutComment($stmt)
    {
        if ($stmt instanceof ClassMethod) {
            return count($stmt->getComments()) <= 0;
        }

        return false;
    }

    protected function assertUse($stmt)
    {
        return $stmt instanceof Use_;
    }

    protected function assertUseRepository($stmt)
    {
        if ($stmt instanceof UseUse) {
            return count(array_intersect(['App', 'Domains', 'Repositories', 'Repository'], $stmt->name->parts)) >= 4;
        }

        return false;
    }

    protected function assertUseModel($stmt)
    {
        if ($stmt instanceof UseUse) {
            if (!$this->assertUseRepository($stmt)) {
                return count(array_intersect(['App', 'Domains', 'Repositories'], $stmt->name->parts)) >= 3;
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
