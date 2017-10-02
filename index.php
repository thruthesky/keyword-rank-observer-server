<?php


include_once "ezSQL/shared/ez_sql_core.php";
include_once "ezSQL/mysqli/ez_sql_mysqli.php";
$db = new ezSQL_mysqli('root','7777','keyword-rank-observer','localhost');


dog($_REQUEST);



save_ranks($_REQUEST);
echo json_encode(['code' => 0]);


function save_ranks( $data ) {
	global $db;
	if ( empty($data) ) fail('no data');
	if ( ! is_array($data) ) fail('data is not array');


	$keyword = $data['keyword'];
	$platform = $data['platform'];


	for ( $j = 0; $j < count($data['rank']); $j ++ ) {
		$e = $data['rank'][$j];
		$rank = $j + 1;
		if ( isset($e['type'] ) ) $type = $e['type'];
		else $type = '';
		if ( $platform == 'desktop' || ( $platform == 'mobile' && $type == 'kin') ) {
			if ( !isset($e['names']) || empty($e['names']) || ! count($e['names']) ) {
				fail('no names');
			}
		}
		else if ( $platform == 'mobile' && $type == 'blog' ) {
			if ( !isset($e['name']) || empty($e['name']) ) fail('no name on blog');
			$e['names'] = [ $e['name'] ];
		}
		else fail('no platform');



		$date = date('Ymd');
		$time = date('Hi');
		$title = $db->escape($e['title']);
		$href = $db->escape($e['href']);


		$names = $e['names'];
		for ( $i = 0; $i < count($names); $i ++ ) {
			$name = $db->escape($names[$i]);
			$q = "
						INSERT INTO keyword_ranks
								(platform, keyword, `date`, `time`, `name`, `rank`, href, title, `type`)
						VALUES
								('$platform', '$keyword', '$date', '$time', '$name', '$rank', '$href', '$title', '$type')
					";
//			dog($q);
			$db->query($q);
		}


	}


}



function fail( $msg ) {
	echo json_encode(['code' => -1, 'message' => $msg]);
	exit;
}



//$user = $_REQUEST['user'];
//$message = $_REQUEST['message'];
//$stamp = time();
//$db->query("INSERT INTO message (user, message, stamp) VALUES ('$user', '$message', $stamp)") ;


function dog( $msg ) {
	return;
	if ( is_integer($msg) || is_string($msg) ) {

	}
	else {
		ob_start();
		print_r($msg);
		$msg = ob_get_clean();
	}
	error_log( $msg, 3, "observer.log");
}
