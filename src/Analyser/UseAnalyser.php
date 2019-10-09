<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class UseAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        $this->analyseClassTypes($stmts);
        $this->analyseUse($stmts);

        return $this;
    }

    protected function analyseUse(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($this->assertUse($element)) {
                foreach ($element->uses as $useStmt) {
                    if ($this->assertUseRepository($useStmt)) {
                        if ($this->isController) {
                            $this->addError($element->getLine(), Errors::USE_REPO_IN_CONTROLLER);
                        }
                        if ($this->isCommand) {
                            $this->addError($element->getLine(), Errors::USE_REPO_IN_COMMAND);
                        }
                    }
                    if ($this->assertUseModel($useStmt)) {
                        if ($this->isController) {
                            $this->addError($element->getLine(), Errors::USE_MODEL_IN_CONTROLLER);
                        }
                        if ($this->isLogic) {
                            $this->addError($element->getLine(), Errors::USE_MODEL_IN_LOGIC);
                        }
                        if ($this->isCommand) {
                            $this->addError($element->getLine(), Errors::USE_MODEL_IN_COMMAND);
                        }
                    }
                }
            }
        });
    }
}
