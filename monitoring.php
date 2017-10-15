<meta http-equiv="refresh" content="30">
<script>
	setTimeout( function() {
		location.reload();
	}, 60 * 1000 );
</script>
<style>
	body {
		font-size: 9pt;
        font-family: "Malgun Gothic", "Gulim", sans-serif;
	}
	.rank span {
		display: inline-block;
		margin: 0 4px;
		background-color: #ddd;
	}
	b[idx] {
		padding: 2px 3px;
	}
	b[idx='0'] {
		background-color:red;
		color: white;
	}
	b[idx='1'] {
		background-color:blue;
		color: white;
	}
	b[idx='2'] {
		background-color:green;
		color: white;
	}
	b[idx='3'] {
		background-color:#444;
		color: white;
	}
    .fs-normal { font-weight: 100; }
    .rank { margin-bottom: .25em; }
    .blog { color: #eee; }
</style>
<form>
<input type="hidden" name="mode" value="monitoring">
<?php
	$keywords = $selectable_keywords;
	$_names = '';
	if ( isset($_REQUEST['keywords']) ) $keywords = $_REQUEST['keywords'];
	else if ( isset($_COOKIE['keywords']) ) $keywords = $_COOKIE['keywords'];

	if ( isset($_REQUEST['names']) ) $_names = $_REQUEST['names'];
	else if ( isset($_COOKIE['names']) ) $_names = $_COOKIE['names'];


?>
키워드: <input name="keywords" size="50" value="<?php echo $keywords?>"> <a href="javascript:alert('모니터링 할 키워드를 입력하세요. 키워드는 지정한 순서대로 나타납니다. 콤마로 여러개 입력 가능. 모든 키워드를 선택하고 싶다면, 키워드를 공백으로 하고, 전송하세요.');">(?)</a>
    이름: <input name="names" size="40" value="<?php echo $_names?>"> <a href="javascript:alert('강조 표시 할 이름을 입력하세요. 콤마로 여러개 입력 가능.');">(?)</a>
<input type="submit" value="Submit">
</form>
<?php

//
//	$q_keywords = '';
//	if ( $keywords ) {
//		$conds = [];
//		foreach ( explode(',', $keywords) as $key ) {
//			$conds[] = "keyword='$key'";
//		}
//		$q_keywords = ' AND (' . implode(' OR ', $conds) . ')';
//	}

	$q_names = [];
	if ( $_names ) {
		foreach ( explode(',', $_names) as $name) {
			$q_names[] = trim($name);
		}
	}




//
//
//	$date = date('Ymd');
//	$time = date('Hi', time() - 10 * 60 );
//    $q = "SELECT * FROM keyword_ranks WHERE `date`='$date' AND `time`>='$time' $q_keywords";
//    $rows = $db->get_results($q, ARRAY_A);

//    echo $q;
//echo count($rows);
//
//	if ( ! $rows ) {
//	    echo "<h3>No data found on the server</h3>";
//	    exit;
//    }
//
//	$data = [];
//	$names = [];
//	foreach( $rows as $row ) {
//		$data[ $row['platform'] ][ $row['keyword'] ][ $row['rank'] ] = $row;
//		$key = $row['platform'] . $row['keyword'] . $row['rank'];
//		if ( ! isset( $names[ $key ] ) ) $names[ $key ] = [];
//		if ( ! in_array( $row['name'], $names[ $key ] ) ) $names[ $key ][] = $row['name'];
//
//	}


?>


<table>
	<tr>
		<td><h1>데스크톱</h1></td>
		<td><h1>모바일</h1></td>
	</tr>
	<tr valign="top">
		<td>
			<?php
			$ks = explode(',', $keywords);
			foreach( $ks as $k ) {
				showKeywords('desktop', $k);
            }
            ?>
		</td>
        <td>
		<?php
		$ks = explode(',', $keywords);
		foreach( $ks as $k ) {
			showKeywords('mobile', $k);
		}
		?>

		</td>

	</tr>
</table>



<?php

function showKeywords( $platform, $keyword ) {
    global $db, $q_names;
	$date = date('Ymd');
	$time = date('Hi', time() - 10 * 60 );
	$q = "SELECT * FROM keyword_ranks WHERE platform='$platform' AND keyword='$keyword' AND `date`='$date' AND `time`>='$time' ORDER BY `idx` ASC";
	$rows = $db->get_results($q, ARRAY_A);
	if ( ! $rows ) return;
	$ranks = [];
	$names = [];
	foreach( $rows as $row ) {
	    $rank = $row['rank'];
	    $name = $row['name'];
	    $ranks[ $rank ] = $row;
	    if ( ! isset( $names[ $rank ] ) ) $names[ $rank ] = [];
	    if ( ! in_array( $name, $names[ $rank ] ) ) $names[ $rank ][] = $name;
    }



    $dt = "Time error";
	$r1 = current($ranks);
	if ( $r1) $dt = date("Y/md H:i a", ymdhis( $r1['date'] . $r1['time'] . "00" ));

	if ( $platform == 'mobile' ) $m = 'm.';
	else $m = '';
    echo "<h2> <a target='_blank' href='https://{$m}search.naver.com/search.naver?where=nexearch&sm=top_hty&fbm=0&ie=utf8&query=$keyword'>$keyword</a> <span class='fs-normal'>$dt</span></h2>";
    for ( $i = 1; $i <= 20; $i ++ ) {
	    if ( ! isset($ranks[$i]) ) continue;
	    $type = $ranks[$i]['type'];
	    $row = $ranks[$i];
	    if ( $type == 'blog' ) $type_display = "[블로그]";
	    else $type_display = "";
	    echo "
            <div class='rank $type'>
            $type_display
            ($row[rank])
            $row[title]
            ";
	    foreach( $names[ $row['rank'] ] as $name ) {
		    $idx = array_search( $name, $q_names );
		    if ( $idx !== false ) {
			    $name = "<b idx=$idx>$name</b>";
		    }
		    echo "<span>$name</span>";
	    }

	    echo "
            </div>
            ";
    }
}


function ymdhis( $datetime ) {
		$Y = substr( $datetime, 0, 4 );
		$m = substr( $datetime, 4, 2 );
		$d = substr( $datetime, 6, 2 );
		$h = substr( $datetime, 8, 2 );
		$i = substr( $datetime, 10, 2 );
		$s = substr( $datetime, 12, 2 );

		return mktime( $h, $i, $s, $m, $d, $Y ) + 9 * 60 * 60;
	}



exit;
