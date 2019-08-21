<?php

namespace Lxj\Review\Bot\Analyser;

use Lxj\Review\Bot\Consts\Errors;

class CommentAnalyser extends Analyser
{
    public function analyse(array $stmts)
    {
        parent::analyse($stmts);

        $this->analyseClassTypes($stmts);
        $this->analyseNoComment($stmts);

        return $this;
    }

    protected function analyseNoComment(array $stmts)
    {
        foreach ($stmts as $stmt) {
            if ($this->assertPublicMethod($stmt) && $this->assertMethodWithoutComment($stmt)) {
                if ($this->isController) {
                    $this->addError($stmt->getLine(), Errors::PUBLIC_CTRL_ME_WITHOUT_COMMENTS);
                }
                if ($this->isLogic) {
                    $this->addError($stmt->getLine(), Errors::PUBLIC_LOGIC_ME_WITHOUT_COMMENTS);
                }
            }

            if (property_exists($stmt, 'stmts')) {
                if (is_array($stmt->stmts) && count($stmt->stmts) > 0) {
                    $this->analyseNoComment($stmt->stmts);
                }
            }
        }
    }
}
