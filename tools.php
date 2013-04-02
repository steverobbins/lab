<?php

ini_set("display_errors", 1);

define("REQUEST_START", microtime(true));

header("Content-Type: text/plain");

array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));

function note($message = '') {

    echo str_pad(round(microtime(true) - REQUEST_START, 3), 10);

    if (is_string($message) || is_numeric($message)) echo $message;
    else echo '[' . gettype($message) . ']';

    echo "\n";
}