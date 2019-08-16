<?php

namespace Lxj\Review\Bot\analyser;

use Lxj\Review\Bot\consts\Errors;

class ExitAnalyser extends Analyser
{
    protected $isController = false;
    protected $isLogic = false;

    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseHasExit($stmts);

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

    protected function analyseHasExit(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertExit($stmt)) {
                if ($this->isController) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::EXIT_IN_CONTROLLER,
                        'msg' => 'Cannot exit in controller.',
                    ]);
                }
                if ($this->isLogic) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::EXIT_IN_LOGIC,
                        'msg' => 'Cannot exit in logic.',
                    ]);
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseHasExit($stmt->stmts);
                }
            }
        }
    }
}
