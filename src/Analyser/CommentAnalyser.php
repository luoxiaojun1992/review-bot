<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class CommentAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        $this->analyseClassTypes($stmts);
        $this->analyseNoComment($stmts);

        return $this;
    }

    protected function analyseNoComment(array $stmts)
    {
        $this->scanElements($stmts, function ($element) {
            if ($this->assertPublicMethod($element) && $this->assertMethodWithoutComment($element)) {
                if ($this->isController) {
                    $this->addError($element->getLine(), Errors::PUBLIC_CTRL_ME_WITHOUT_COMMENTS);
                }
                if ($this->isLogic) {
                    $this->addError($element->getLine(), Errors::PUBLIC_LOGIC_ME_WITHOUT_COMMENTS);
                }
            }
        });
    }
}
