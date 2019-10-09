<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

abstract class Analyser
{
    protected $errors = [];

    protected $filePath;
    protected $ignored;

    protected $isController = false;
    protected $isLogic = false;
    protected $isCommand = false;

    public function __construct($filePath, $ignored = [], $options = [])
    {
        $this->filePath = $filePath;
        $this->ignored = $ignored;
        $this->setOptions($options);
    }

    protected function scanElements($elements, $handler, &$scanned = [])
    {
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $this->scanElements($element, $handler, $scanned);
            }
        } elseif (is_object($elements)) {
            $elementId = spl_object_id($elements);
            if (in_array($elementId, $scanned)) {
                return;
            }
            $scanned[] = $elementId;

            call_user_func_array($handler, [$elements]);
            foreach ($elements as $property) {
                $this->scanElements($property, $handler, $scanned);
            }
        }
    }

    protected function setOptions($options)
    {
        foreach ($options as $optionName => $optionValue) {
            if (property_exists($this, $optionName)) {
                $this->{$optionName} = $optionValue;
            }
        }

        return $this;
    }

    abstract public function analyse(array $stmts);

    protected function analyseClassTypes(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($this->assertController($element)) {
                $this->isController = true;
            } elseif ($this->assertLogic($element)) {
                $this->isLogic = true;
            } elseif ($this->assertCommand($element)) {
                $this->isCommand = true;
            }
        });
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

    protected function addError($line, $code, $file = null, $msg = null, $chineseMsg = null)
    {
        $file = $file ?: $this->filePath;
        if (!in_array(implode(':', [$file, $line, $code]), $this->ignored)) {
            $this->errors[] = [
                'file' => $file,
                'line' => $line,
                'code' => $code,
                'msg' => $msg ?: Errors::message($code),
                'chinese_msg' => $chineseMsg ?: Errors::chineseMessage($code),
            ];
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
