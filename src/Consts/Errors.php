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
    const METHOD_TOO_LARGE = 14;

    const MESSAGES = [
        self::ECHO_IN_CONTROLLER => 'Cannot echo in controller.',
        self::ECHO_IN_LOGIC => 'Cannot echo in logic.',
        self::EXIT_IN_CONTROLLER => 'Cannot exit in controller.',
        self::EXIT_IN_LOGIC => 'Cannot exit in logic.',
        self::PUBLIC_CTRL_ME_WITHOUT_COMMENTS => 'Public controller method without comments.',
        self::PUBLIC_LOGIC_ME_WITHOUT_COMMENTS => 'Public logic method without comments.',
        self::USE_REPO_IN_CONTROLLER => 'Cannot use repo in controller.',
        self::USE_MODEL_IN_CONTROLLER => 'Cannot use model in controller.',
        self::USE_MODEL_IN_LOGIC => 'Cannot use model in logic.',
        self::USE_REPO_IN_COMMAND => 'Cannot use repo in command.',
        self::USE_MODEL_IN_COMMAND => 'Cannot use model in command.',
        self::ME_ARGS_WITH_DEFAULT_VALUE => 'Method arguments with default values MUST go at the end of the argument list.',
        self::RET_API_FORMAT_DATA_IN_LOGIC => 'Cannot return api format data in logic.',
        self::METHOD_TOO_LARGE => 'Method is too large.',
    ];

    const CHINESE_MESSAGES = [
//        self::ECHO_IN_CONTROLLER => '',
//        self::ECHO_IN_LOGIC => '',
//        self::EXIT_IN_CONTROLLER => '',
//        self::EXIT_IN_LOGIC => '',
//        self::PUBLIC_CTRL_ME_WITHOUT_COMMENTS => '',
//        self::PUBLIC_LOGIC_ME_WITHOUT_COMMENTS => '',
//        self::USE_REPO_IN_CONTROLLER => '',
//        self::USE_MODEL_IN_CONTROLLER => '',
//        self::USE_MODEL_IN_LOGIC => '',
//        self::USE_REPO_IN_COMMAND => '',
//        self::USE_MODEL_IN_COMMAND => '',
//        self::ME_ARGS_WITH_DEFAULT_VALUE => '',
//        self::RET_API_FORMAT_DATA_IN_LOGIC => '',
//        self::METHOD_TOO_LARGE => '',
    ];

    public static function message($code)
    {
        return self::MESSAGES[$code] ?? 'Unknown error';
    }

    public static function chineseMessage($code)
    {
        return self::CHINESE_MESSAGES[$code] ?? self::message($code);
    }
}
