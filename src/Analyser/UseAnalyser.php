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
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_REPO_IN_CONTROLLER,
                                'msg' => 'Cannot use repo in controller',
                            ]);
                        }
                        if ($this->isLogic) {
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_REPO_IN_LOGIC,
                                'msg' => 'Cannot use repo in logic',
                            ]);
                        }
                        if ($this->isCommand) {
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_REPO_IN_COMMAND,
                                'msg' => 'Cannot use repo in command',
                            ]);
                        }
                    }
                    if ($this->assertUseModel($useStmt)) {
                        if ($this->isController) {
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_MODEL_IN_CONTROLLER,
                                'msg' => 'Cannot use model in controller',
                            ]);
                        }
                        if ($this->isLogic) {
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_MODEL_IN_LOGIC,
                                'msg' => 'Cannot use model in logic',
                            ]);
                        }
                        if ($this->isCommand) {
                            $this->addError([
                                'file' => $this->filePath,
                                'line' => $stmt->getLine(),
                                'code' => Errors::USE_MODEL_IN_COMMAND,
                                'msg' => 'Cannot use model in command',
                            ]);
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
