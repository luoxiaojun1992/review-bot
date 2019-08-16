<?php

namespace Lxj\Review\Bot\analyser;

class EchoAnalyser extends Analyser
{
    protected $isController = false;
    protected $isLogic = false;

    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseHasEcho($stmts);

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

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseClassTypes($stmt->stmts);
                }
            }
        }
    }

    protected function analyseHasEcho(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertEcho($stmt)) {
                if ($this->isController) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'msg' => 'Cannot echo in controller.',
                    ]);
                }
                if ($this->isLogic) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'msg' => 'Cannot echo in logic.',
                    ]);
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseHasEcho($stmt->stmts);
                }
            }
        }
    }
}
