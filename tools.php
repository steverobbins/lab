<?php

ini_set("display_errors", 1);

define("REQUEST_START", microtime(true));

header("Content-Type: text/plain");

// clears terminal
if (php_sapi_name() == 'cli') array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));

function note($message = '', $showTime = true, $alwaysEcho = true) {

    $output = '';

    if ($showTime) {
        $time = microtime(true) - $this->_startTime;
        if ($time > 60 * 60) {
            $time /= 60 * 60;
            $stamp = 'h';
        }
        else if ($time  > 60) {
            $time /= 60;
            $stamp = 'm';
        }
        else {
            $stamp = 's';
        }
        $output .= str_pad((float)round($time, 3) . " " . $stamp, 12);
    }

    $canEcho = true;
    if (is_string($message) || is_numeric($message)) $output .= $message;
    else if (is_bool($message)) $output .= $message ? "true" : "false";
    else {
        $canEcho = false;
        $output .= "[" . gettype($message) . "]";
    }

    $output .= "\n";

    if ($canEcho || $alwaysEcho) echo $output;

    return $canEcho;
}

function getParam($key) {

    if (isset($_GET) && isset($_GET[$key])) return $_GET[$key];
    if (strlen($key) == 1) $args = getopt($key . "::");
    else $args = getopt("", array($key . "::"));

    if (!$args) return false;
    $args = array_values($args);
    return isset($args[0]) ? $args[0] : false;
}