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
        return date('h:ia', mktime((int)$h, (int)$m));

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


function prepareGraph($rows, $target_name = null)
{

    $res = [];
    $prev_time = null;
    foreach ($rows as $row) {

        $time = $row->time;
        if (!$prev_time) $prev_time = $time;
        $timeInterval = $time - $prev_time;
        if (($timeInterval) > 6 && $timeInterval < 45) {
            $status = false;
        } else {
            $status = true;
        }
        $prev_time = $time;

        if ($target_name && strpos($row->name, $target_name) !== 0) continue;

        $title = $row->title;
        $name = $row->name;
        $date = $row->date;
        $keyword = $row->keyword;
        $platform = $row->platform;


//        if (!isset($res[$title])) $res[$title] = ['keyword' => $keyword, 'dates' => []];
        if (!isset($res[$title])) $res[$title] = [
            'keyword' => $keyword,
            'platform' => []
        ];
        if (!isset($res[$title]['platform'][$platform])) $res[$title]['platform'][$platform] = [];
        if (!isset($res[$title]['platform'][$platform]['names'])) $res[$title]['platform'][$platform]['names'] = [];
        if (!in_array($name, $res[$title]['platform'][$platform]['names'])) array_push($res[$title]['platform'][$platform]['names'], $name);


        if (!isset($res[$title]['platform'][$platform]['dates'][$date])) $res[$title]['platform'][$platform]['dates'][$date] = [];

        $res[$title]['platform'][$platform]['dates'][$date][$time] = [
            'status' => $status,
            'rank' => $row->rank
        ];


//		if ( count($res[$title]['platform'][$platform]['dates']) > 1 ) continue; // TEST CODe
//		$res[ $title ][ $name ][] = $data;
    }

//    echo "<pre>";
//    print_r($res);
//    exit;

    return $res;
}