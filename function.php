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
	foreach ( $rows as $row ) {
		if ( $target_name && strpos($row->name, $target_name) !== 0 ) continue;

		$title = $row->title;
		$name = $row->name;
		$date = $row->date;
		$time = $row->time;
		$keyword = $row->keyword;



		if ( ! isset($res[$title]) ) $res[$title] = [ 'rank' => $row->rank, 'keyword' => $keyword , 'dates' => [] ];
		if ( ! isset( $res[$title][ 'names' ] ) ) $res[$title][ 'names' ] = [];
		if ( !in_array($name, $res[$title]['names']) ) array_push($res[$title]['names'], $name);


		if ( ! isset($res[$title]['dates'][$date]) ) $res[$title]['dates'][$date] = [];
		$res[$title]['dates'][$date][] = $time;


////		if ( count($res[$title][$name]) > 10 ) continue; // TEST CODe
//		$res[ $title ][ $name ][] = $data;
	}
//
//	echo "<pre>";
//	print_r($res);
//	exit;

	return $res;


}