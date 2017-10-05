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

function add0($num = 0, $postfix = '')
{
    return $num < 10 ? '0' . $num . $postfix : '' . $num . $postfix;
}

function showTime($time)
{
    if (strlen((string)$time) <= 2) {
        return date('ha', mktime($time));
    } else {
        $h = getHour($time);
        $m = getMinute($time);
        return date('h:ia', mktime($h, $m));

    }
}

function getYear($date)
{
    return substr($date, 0, 4);
}

function getMonth($date)
{
    return substr($date, 4, 2);
}

function getDay($date)
{
    return substr($date, 6, 2);
}

function getHour($time)
{
    return substr($time, 0, 2);
}

function getMinute($time)
{
    return substr($time, 2, 2);
}


function prepareGraph($rows, $name = null)
{
    $titles = [];
    $graphs = [];
    foreach ($rows as $row) {

        if ( $name && $row->name != $name) continue;

        $title = $row->title;
        if (!in_array($title, $titles)) {
            $titles[] = $title;
        }

        $index = array_search($title, $titles);

        $data = [
            'rank' => $row->rank,
            'date' => $row->date,
            'time' => $row->time,
            'keyword' => $row->keyword,
            'name' => $row->name
        ];


        $graphs[$index]['data'][] = $data;
        if(empty($graphs[$index]['title'])) $graphs[$index]['title'] = $title;
    }
//    echo "<pre>";
//    print_r($graphs);
//    echo "</pre>";

    return $graphs;
}