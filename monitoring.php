<meta http-equiv="refresh" content="30">
<script>
	setTimeout( function() {
		location.reload();
	}, 60 * 1000 );
</script>
<style>
	body {
		font-size: 9pt;
	}
	span {
		display: inline-block;
		margin: 0 4px;
		background-color: #ddd;
		border-radious: 3px;
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
</style>
<form>
<input type="hidden" name="mode" value="monitoring">
<?php
	$keywords = '';
	$_names = '';
	if ( isset($_REQUEST['keywords']) ) $keywords = $_REQUEST['keywords'];
	else if ( isset($_COOKIE['keywords']) ) $keywords = $_COOKIE['keywords'];
	if ( isset($_REQUEST['names']) ) $_names = $_REQUEST['names'];
	else if ( isset($_COOKIE['names']) ) $_names = $_COOKIE['names'];

?>
Keywords: <input name="keywords" size="50" value="<?php echo $keywords?>">
Names: <input name="names" size="40" value="<?php echo $_names?>">
<input type="submit" value="Submit">
</form>
<?php


	$q_keywords = '';
	if ( $keywords ) {
		$conds = [];
		foreach ( explode(',', $keywords) as $key ) {
			$conds[] = "keyword='$key'";
		}
		$q_keywords = ' AND (' . implode(' OR ', $conds) . ')';
	}

	$q_names = [];
	if ( $_names ) {
		foreach ( explode(',', $_names) as $name) {
			$q_names[] = trim($name);
		}
	}





	$date = date('Ymd');
	$time = date('Hi', time() - 10 * 60 );
$q = "SELECT keyword FROM keyword_ranks WHERE `date`='$date' AND `time`>'$time' GROUP BY keyword";
$rows = $db->get_results( $q, ARRAY_N );
?>
Selectable Keywords: <?php foreach( $rows as $row ) echo $row[0] . ', ' ?>

<?php
    $q = "SELECT * FROM keyword_ranks WHERE `date`='$date' AND `time`>='$time' $q_keywords";
    $rows = $db->get_results($q, ARRAY_A);

//echo count($rows);

	if ( ! $rows ) return;

	$data = [];
	$names = [];
	foreach( $rows as $row ) {
		$data[ $row['platform'] ][ $row['keyword'] ][ $row['rank'] ] = $row;
		$key = $row['platform'] . $row['keyword'] . $row['rank'];
		if ( ! isset( $names[ $key ] ) ) $names[ $key ] = [];
		if ( ! in_array( $row['name'], $names[ $key ] ) ) $names[ $key ][] = $row['name'];
		
	}

//echo "<pre>";
//print_r($data);
?>


<table>
	<tr>
		<td><h1>Desktop</h1></td>
		<td><h1>Mobile</h1></td>
	</tr>
	<tr valign="top">
		<td>
	
			<?php
				if ( isset($data['desktop']) ) {
					foreach( $data['desktop'] as $keyword => $rows ) {
						$date = date("Y/m/d H:i a", ymdhis($rows[1]['date']. $rows[1]['time'] ."00"));
?>
<h2><?php echo $keyword . "$date"?></h2>
<?php
						for( $i = 0; $i < count($rows); $i ++ ) {
							$row = $rows[$i+1];
?>
<div>
(<?php echo $row['rank']?>) <?php echo $row['title']?>
<?php foreach( $names[ 'desktop' . $keyword . $row['rank'] ] as $name ) {
	$idx = array_search( $name, $q_names );
	if ( $idx !== false ) $name = "<b idx=$idx>$name</b>";
	echo "<span>$name</span>";
}?>
</div>
<?php
						}
					}
				}
?>
		</td>







		<td>
	
			<?php
				if ( isset($data['mobile']) ) {
					foreach( $data['mobile'] as $keyword => $rows ) {
						$date = date("Y/md H:i a", ymdhis($rows[1]['date']. $rows[1]['time'] ."00"));
?>
<h2><?php echo $keyword . "$date"?></h2>
<?php
						for( $i = 0; $i < count($rows); $i ++ ) {
							$row = $rows[$i+1];
?>
<div>
(<?php echo $row['rank']?>) <?php echo $row['title']?>
<?php foreach( $names[ 'mobile' . $keyword . $row['rank'] ] as $name ) {
	$idx = array_search( $name, $q_names );
	if ( $idx !== false ) $name = "<b idx=$idx>$name</b>";
	echo "<span>$name</span>";
}?>
</div>
<?php
						}
					}
				}
?>
		</td>

	</tr>
</table>



<?php


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
