<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class UseAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseUse($stmts);

        return $this;
    }

    protected function analyseUse(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertUse($stmt)) {
                foreach ($stmt->uses as $useStmt) {
                    if ($this->assertUseRepository($useStmt)) {
                        if ($this->isController) {
                            $this->addError($stmt->getLine(), Errors::USE_REPO_IN_CONTROLLER);
                        }
                        if ($this->isCommand) {
                            $this->addError($stmt->getLine(), Errors::USE_REPO_IN_COMMAND);
                        }
                    }
                    if ($this->assertUseModel($useStmt)) {
                        if ($this->isController) {
                            $this->addError($stmt->getLine(), Errors::USE_MODEL_IN_CONTROLLER);
                        }
                        if ($this->isLogic) {
                            $this->addError($stmt->getLine(), Errors::USE_MODEL_IN_LOGIC);
                        }
                        if ($this->isCommand) {
                            $this->addError($stmt->getLine(), Errors::USE_MODEL_IN_COMMAND);
                        }
                    }
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseUse($stmt->stmts);
                }
            }
        }
    }
}
