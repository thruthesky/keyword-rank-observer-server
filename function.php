<?php
/**
 *
 * @note By default it returns null if the key does not exist.
 *
 * @param $name
 * @param null $default
 * @return null
 *
 */
function in($name, $default = null)
{
    if (isset($_POST[$name])) return $_POST[$name];
    else if (isset($_GET[$name])) return $_GET[$name];
    else return $default;
}

function add0($num = 0, $postfix = '') {
    return $num < 10 ? '0' . $num . $postfix : '' . $num . $postfix;
}

function showTime($time) {
    if ($time <= 0 || $time >= 24) return '12am';
    else if ($time < 12) return $time . 'am';
    else if ($time == 12) return '12pm';
    else return $time - 12 . 'pm';
}