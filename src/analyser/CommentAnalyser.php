<?php

namespace Lxj\Review\Bot\analyser;

use Lxj\Review\Bot\consts\Errors;

class CommentAnalyser extends Analyser
{
    protected $isController = false;
    protected $isLogic = false;

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
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::PUBLIC_CTRL_ME_WITHOUT_COMMENTS,
                        'msg' => 'Public controller method without comments',
                    ]);
                }
                if ($this->isLogic) {
                    $this->addError([
                        'file' => $this->filePath,
                        'line' => $stmt->getLine(),
                        'code' => Errors::PUBLIC_LOGIC_ME_WITHOUT_COMMENTS,
                        'msg' => 'Public logic method without comments',
                    ]);
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
