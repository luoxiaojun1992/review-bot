<?php

return [
    \Lxj\Review\Bot\Analyser\EchoAnalyser::class,
    \Lxj\Review\Bot\Analyser\ExitAnalyser::class,
    \Lxj\Review\Bot\Analyser\CommentAnalyser::class,
    \Lxj\Review\Bot\Analyser\UseAnalyser::class,
    [\Lxj\Review\Bot\Analyser\ParameterAnalyser::class, ['argumentLengthLimit' => 10]],
    \Lxj\Review\Bot\Analyser\ReturnAnalyser::class,
    [\Lxj\Review\Bot\Analyser\MethodAnalyser::class, ['methodLinesLimit' => 500]],
];
