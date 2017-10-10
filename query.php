<?php
if ( isset($_REQUEST['keywords']) ) setcookie("keywords", $_REQUEST['keywords'], time() + 365 * 24 * 60 * 60 );
if (isset($_REQUEST['names']) ) setcookie("names", $_REQUEST['names'], time() + 365 * 24 * 60 * 60 );
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/query.css">
</head>
<body>
<?php
include 'db.php';
include 'function.php';
?>
<h2>
<a href="?mode=">Keyword Rank Statistics</a>
<a href="?mode=monitoring">Realtime Keyword Monitoring</a>
</h2>
<?php
	$date = date('Ymd');
	$time = date('Hi', time() - 10 * 60 );
$q = "SELECT keyword FROM keyword_ranks WHERE `date`='$date' AND `time`>'$time' GROUP BY keyword";
$rows = $db->get_results( $q, ARRAY_N );
?>
Selectable Keywords: <?php foreach( $rows as $row ) echo $row[0] . ', ' ?>

<?php

if ( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'monitoring' ) require 'monitoring.php';

$colors = ['red', 'lightgreen', 'blue', 'violet', 'yellow', 'grey',  'black'];
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
$hourStart = '0000';
$hourEnd = '2400';
$yearStart = date('Y');
$yearEnd = $yearStart;
$monthStart = date('n');
$monthEnd = $monthStart;
$dayStart = date('j');
$dayEnd = $dayStart;

if (in('monthStart')) $monthStart = in('monthStart');
if (in('monthEnd')) $monthEnd = in('monthEnd');

if (in('dayStart')) $dayStart = add0(in('dayStart'));
if (in('dayEnd')) $dayEnd = add0(in('dayEnd'));

if (in('yearStart')) $yearStart = add0(in('yearStart'));
if (in('yearEnd')) $yearEnd = add0(in('yearEnd'));

if (in('hourStart')) $hourStart = in('hourStart');
if (in('hourEnd')) $hourEnd = in('hourEnd');

$daysInMonthStart = date('t', mktime(0, 0, 0, $monthStart, 1, $yearStart));
$daysInMonthEnd = date('t', mktime(0, 0, 0, $monthEnd, 1, $yearEnd));

if ($dayStart > $daysInMonthStart) $dayStart = $daysInMonthStart;
if ($dayEnd > $daysInMonthEnd) $dayEnd = $daysInMonthEnd;

$keyword = in('keyword');
//$platform = in('platform');
$name = in('name');
$desktop = in('desktop');
$mobile = in('mobile');


$dateStart = strtotime("$yearStart-$monthStart-$dayStart " . getHour($hourStart) . ':' . getMinute($hourStart));
$dateEnd = strtotime("$yearEnd-$monthEnd-$dayEnd " . getHour($hourEnd) . ':' . getMinute($hourEnd));

$dateInterval = ($dateEnd - $dateStart) / 300;
if ($dateInterval < 0) echo "Date Start cant be ahead of Date End";
else {
    $where = '1';
    $where .= " AND (`date` BETWEEN $yearStart$monthStart$dayStart AND $yearEnd$monthEnd$dayEnd)";
    if ($hourStart <= $hourEnd) $where .= " AND (`time` BETWEEN $hourStart AND $hourEnd)";
    if ($keyword) $where .= " AND keyword = '$keyword'";

    if ($desktop != $mobile) {
        if ($desktop) {
            $where .= " AND platform = 'desktop'";
        } else $where .= " AND (platform = 'mobile' AND `type`='kin')";
    }
    else {
        $where .= " AND (platform = 'desktop' OR (platform = 'mobile' AND `type`='kin'))";
    }
//    if ($platform) $where .= " AND platform = '$platform'";
//    if ($name) $where .= " AND `name` LIKE '$name%'";

    $q = "SELECT idx, platform, keyword, `date`, `time`, `name`, title, `type`, `rank` FROM keyword_ranks WHERE $where";
    //print_r("$q<br>");


    $rows = $db->get_results($q);

    if (!empty($rows)) $graphs = prepareGraph($rows, $name);
}

?>

<form method="GET">
    <nav class="searchOption list">
        <ul>
            <li>
                Date Start
                <div>
                    <select name="monthStart" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option value="<?php echo add0($i) ?>" <?php if ($i == $monthStart) echo ' selected' ?>><?php echo $months[$i - 1] ?></option>
                        <?php } ?>
                    </select>
                    <select name="dayStart" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= $daysInMonthStart; $i++) { ?>
                            <option value="<?php echo $i ?>" <?php if ($i == $dayStart) echo ' selected' ?>><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                    <select name="yearStart" onchange="this.form.submit()">
                        <?php for ($i = 3; $i >= 0; $i--) {
                            $year = date('Y') - $i;
                            ?>
                            <option value="<?php echo $year ?>" <?php if ($year == $yearStart) echo ' selected' ?>><?php echo $year ?></option>
                        <?php } ?>
                    </select>
                    <select name="hourStart" onchange="this.form.submit()">
                        <?php
                        $hs = 0;
                        for ($i = 0; $i < 24; $i++) {
                            $hr = add0($i, '00');
                            ?>
                            <option value="<?php echo $hr ?>" <?php if ($hr == $hourStart) echo ' selected' ?>><?php echo showTime($i) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </li>
            <li>
                Date End
                <div>
                    <select name="monthEnd" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option value="<?php echo add0($i) ?>" <?php if ($i == $monthEnd) echo ' selected' ?>><?php echo $months[$i - 1] ?></option>
                        <?php } ?>
                    </select>
                    <select name="dayEnd" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= $daysInMonthEnd; $i++) { ?>
                            <option value="<?php echo $i ?>" <?php if ($i == $dayEnd) echo ' selected' ?>><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                    <select name="yearEnd" onchange="this.form.submit()">
                        <?php for ($i = 3; $i >= 0; $i--) {
                            $year = date('Y') - $i;
                            ?>
                            <option value="<?php echo $year ?>" <?php if ($year == $yearEnd) echo ' selected' ?>><?php echo $year ?></option>
                        <?php } ?>
                    </select>
                    <select name="hourEnd" onchange="this.form.submit()">
                        <?php
                        for ($i = 1; $i <= 24; $i++) {
                            $hr = add0($i, '00');
                            ?>
                            <option value="<?php echo $hr ?>" <?php if ($hr == $hourEnd) echo ' selected' ?>><?php echo showTime($i) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </li>
            <li>
                Keyword
                <div>
                    <input type="text" name="keyword" value="<?php echo $keyword ?>">
                </div>
            </li>
            <li>
                Platform
                <div>
                    <label for="desktop">Desktop
                        <input id="desktop" type="checkbox"
                               name="desktop" <?php if ($desktop == 'on') echo ' checked' ?>
                               onchange="this.form.submit()">
                    </label>
                    <label for="mobile">Mobile
                        <input id="mobile" type="checkbox" name="mobile" <?php if ($mobile == 'on') echo ' checked' ?>
                               onchange="this.form.submit()"
                    </label>
                </div>
            </li>
            <li>
                Name
                <div>
                    <input type="text" name="name" value="<?php echo $name ?>">
                </div>
            </li>
        </ul>
    </nav>
    <input type="submit" value="Search">
</form>

<br>

<div class="list legend">
    <ul>
        <li>
            <span class="box" style="color: <?php echo $colors[0] ?>">■</span>Rank 1
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[1] ?>">■</span>Rank 2
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[2] ?>">■</span>Rank 3
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[3] ?>">■</span>Rank 4
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[4] ?>">■</span>Rank 5
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[5] ?>">■</span>No Appearance
        </li>
        <li>
            <span class="box" style="color: <?php echo $colors[6] ?>">■</span>No Data
        </li>
    </ul>
</div>


<?php

$dHeight = 200;
$npHeight = 20;
$npTop = $dHeight - $npHeight;

if (!empty($graphs)) {
//        echo "<pre>";
//        print_r($graphs); exit;
    foreach ($graphs as $title => $platforms) {
        if (empty($platforms)) continue;
//
//        echo "<pre>";
//        print_r($platforms); exit;

        foreach ($platforms['platform'] as $platform => $graph) {

//            echo "<pre>";
//            print_r($graph);
            $names = '';
            if(!empty($graph['names'] ) )$names =  implode(',', $graph['names']);
            $header = "$platform - $title. " . $names;
            echo "<h3>$header</h3>";


            $y = 0;
            $m = 0;
            $d = 0;
            $h = 0;
            $min = 0;
            $daysInterval = $dayEnd - $dayStart;

            for ($i = 0; $i <= $daysInterval; $i++) {
                $dates = date("Ymd", mktime(0, 0, 0, $monthStart, $dayStart + $i, $yearStart));
                if (!array_key_exists($dates, $graph['dates'])) {
                    $dStart = strtotime("$monthStart-$monthStart-$dayStart " . getHour($hourStart) . ':' . getMinute($hourStart));
                    $dEnd = strtotime("$monthStart-$monthStart-$dayStart " . getHour($hourEnd) . ':' . getMinute($hourEnd));
                    $dInterval = floor((($dEnd - $dStart) / 300));
                    $hour = getHour($hourStart);
                    $minutes = getMinute($hourStart);
                    for ($in = 0; $in < $dInterval; $in++) {
                        $ctime = date("Hi", mktime($hour, $minutes + $in * 5, 0));
                        $graph['dates'][$dates][$ctime] = [
                            'status' => false,
                            'rank' => 7
                        ];
                    }
                }
            }
            ksort($graph['dates'], 1);
//
//            echo "<pre>";
//            print_r($graph['dates']);
//            exit;

            echo "<div class=\"statisticGraph\">";
            echo "<div class=\"bar\">";
            foreach ($graph['dates'] as $date => $times) {
                $y = getYear($date);
                $m = getMonth($date);
                $d = getDay($date);
                $indicator = $dates = date("M d Y", mktime(0, 0, 0, $m, $d, $y));
                $dateStart = strtotime("$y-$m-$d " . getHour($hourStart) . ':' . getMinute($hourStart));
                echo "<div class=\"day\">";
                echo "<div class='indicator'><span class='leftArrow'></span>$indicator<span class='rightArrow'></span></div>";


                foreach ($times as $time => $re) {


                    $h = getHour($time);
                    $min = getMinute($time);
                    $currentDate = date("M d Y", mktime($h, $min, 0, $m, $d, $y));
                    $dateEnd = strtotime("$y-$m-$d " . $h . ':' . $min);
                    $dateInterval = floor((($dateEnd - $dateStart) / 300));

                    /**
                     * Red lines from start and in between
                     */
                    for ($i = 1; $i < $dateInterval; $i++) {
                        $currentTime = date("Hi", mktime($h, $min + ($i - 1) * 5, 0));

                        print_r("$currentTime = $time");
                        if ((int)$currentTime > (int)$hourEnd) break;
                        if ((int)$currentTime < (int)$hourStart) continue;
                        $now = date("M d Y h:ia", mktime($h, $min + ($i - 1) * 5, 0, $monthStart, $dayStart, $yearStart));
                        $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[5];' title='$now'";
                        echo "<span $attr></span>";
                    }

                    $rank = $re['rank'];
                    $info = 'Rank: ' . $rank
                        . ' Keyword: ' . $platforms['keyword'] . ', '
                        . ' Name: ' . $name . ', '
                        . $currentDate . ' ' . showTime($time);
                    $height = (6 - $rank) * ($dHeight / 5);
                    $top = $dHeight - $height;
                    $color = $colors[(int)$rank - 1];

                    if ($re['status']) {
                        $data = "style='height:$height" . "px; margin-top:$top" . "px; background-color: $color;' title='$info'";
                    } else {
                        $data = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[6];' title='$info'";
                    }
                    echo "<span $data></span>";
                    $dateStart = $dateEnd;

                }

                $dateEnd = strtotime("$y-$m-$d " . getHour($hourEnd) . ':' . getMinute($hourEnd));
                $dateInterval = ($dateEnd - $dateStart) / 300;
                /**
                 * Red lines after the record until the end of selected date
                 */
                for ($i = 1; $i < $dateInterval; $i++) {
                    $currentTime = date("Hi", mktime($h, $min + $i * 5, 0));
                    if ($currentTime < $hourStart || $currentTime > $hourEnd || $currentTime == '2400') continue;
                    $now = date("M d Y h:ia", mktime($h, ($i * 5) + $min, 0, $m, $d, $y));
                    $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[6];' title='$now'";
                    echo "<span $attr></span>";
                }


                echo "</div>";
            }
            echo "</div>";
            echo "</div>";

        }


    }

} else {

    echo "<h3>\"NO RECORD FOUND SERVER MIGHT BE DOWN. TRY CHOOSING EARLIER DATE\"</h3>";
}


?>

<ul>
    <li>
        키워드를 입력하고, 이름을 입력하지 않으면, 해당 기간의 모든 광고(순위)글을 볼 수 있습니다.
    </li>
    <li>
        이름만 입력하고 (키워드를 입력하지 않고) 검색하면, 해당 기간에서 해당 이름으로 광고한 모든 (순위)글을 볼 수 있습니다.
    </li>
    <li>Rank 가 0 인 것은 광고가 노출되지 않은 것이다.</li>
    <li>순위 집계가 되지 않은 기간을 파악 할 수 있습니다.</li>
</ul>

</body>
</html>

