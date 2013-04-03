<?php

ini_set("display_errors", 1);

define("REQUEST_START", microtime(true));

header("Content-Type: text/plain");

// clears terminal
if (php_sapi_name() == 'cli') array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));

function note($message = '', $time = true) {

    if ($time) echo str_pad((float)round(microtime(true) - REQUEST_START, 3), 10);

    $canEcho = true;
    if (is_string($message) || is_numeric($message)) echo $message;
    else {
        $canEcho = false;
        var_dump($message);
    }

    if ($canEcho) echo "\n";
}