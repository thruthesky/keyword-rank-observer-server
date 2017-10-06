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


function prepareGraph($rows, $target_name = null)
{

	$res = [];
	foreach ( $rows as $row ) {
		if ( $target_name && strpos($row->name, $target_name) !== 0 ) continue;

		$title = $row->title;
		$name = $row->name;


		if ( ! isset($res[$title]) ) $res[$title] = [];
		if ( ! isset( $res[$title][ $name ] ) ) $res[$title][ $name ] = [];

		$data = [
			'rank' => $row->rank,
			'date' => $row->date,
			'time' => $row->time,
			'keyword' => $row->keyword
		];

//		if ( count($res[$title][$name]) > 10 ) continue; // TEST CODe
		$res[ $title ][ $name ][] = $data;


	}

	
	return $res;



//    $titles = [];
//    $graphs = [];
//    foreach ($rows as $row) {
////        print_r(strpos($row->name, $name));
//        if ( $name && strpos($row->name, $name) !== 0 ) continue;
//
//        $title = $row->title;
//        if (!in_array($title, $titles)) {
//            $titles[] = $title;
//        }
//
//        $index = array_search($title, $titles);
//
//        $data = [
//            'rank' => $row->rank,
//            'date' => $row->date,
//            'time' => $row->time,
//            'keyword' => $row->keyword,
//            'name' => $row->name
//        ];
//
//
//        $graphs[$index]['data'][] = $data;
//        if(empty($graphs[$index]['title'])) $graphs[$index]['title'] = $title;
//        if(empty($graphs[$index]['names']) || !in_array($row->name,$graphs[$index]['names'])) $graphs[$index]['names'][] = $row->name;
//    }
//
//
//
//    return $graphs;
}