<?php

namespace Lxj\Review\Bot\Consts;

class Errors
{
    const ECHO_IN_CONTROLLER = 1;
    const ECHO_IN_LOGIC = 2;
    const EXIT_IN_CONTROLLER = 3;
    const EXIT_IN_LOGIC = 4;
    const PUBLIC_CTRL_ME_WITHOUT_COMMENTS = 5;
    const PUBLIC_LOGIC_ME_WITHOUT_COMMENTS = 6;
    const USE_REPO_IN_CONTROLLER = 7;
    const USE_MODEL_IN_CONTROLLER = 8;
    const USE_MODEL_IN_LOGIC = 9;
    const USE_REPO_IN_COMMAND = 10;
    const USE_MODEL_IN_COMMAND = 11;
    const ME_ARGS_WITH_DEFAULT_VALUE = 12;
    const RET_API_FORMAT_DATA_IN_LOGIC = 13;
}
