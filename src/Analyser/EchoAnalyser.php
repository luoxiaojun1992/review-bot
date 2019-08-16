<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class EchoAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseHasEcho($stmts);

        return $this;
    }

    protected function analyseHasEcho(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertEcho($stmt)) {
                if ($this->isController) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::ECHO_IN_CONTROLLER,
                        'msg' => 'Cannot echo in controller.',
                    ]);
                }
                if ($this->isLogic) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::ECHO_IN_LOGIC,
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
