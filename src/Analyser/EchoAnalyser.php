<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class EchoAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        $this->analyseClassTypes($stmts);
        $this->analyseHasEcho($stmts);

        return $this;
    }

    protected function analyseHasEcho(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($this->assertEcho($element)) {
                if ($this->isController) {
                    $this->addError($element->getLine(), Errors::ECHO_IN_CONTROLLER);
                }
                if ($this->isLogic) {
                    $this->addError($element->getLine(), Errors::ECHO_IN_LOGIC);
                }
            }
        });
    }
}
