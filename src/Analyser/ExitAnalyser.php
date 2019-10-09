<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class ExitAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        $this->analyseClassTypes($stmts);
        $this->analyseHasExit($stmts);

        return $this;
    }

    protected function analyseHasExit(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($this->assertExit($element)) {
                if ($this->isController) {
                    $this->addError($element->getLine(), Errors::EXIT_IN_CONTROLLER);
                }
                if ($this->isLogic) {
                    $this->addError($element->getLine(), Errors::EXIT_IN_LOGIC);
                }
            }
        });
    }
}
