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


$colors = ['lightgreen', 'blue', 'violet', 'yellow', 'orange', 'red', 'black'];
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
$platform = in('platform');
$name = in('name');


$dateStart = strtotime("$yearStart-$monthStart-$dayStart " . getHour($hourStart) . ':' . getMinute($hourStart));
$dateEnd = strtotime("$yearEnd-$monthEnd-$dayEnd " . getHour($hourEnd) . ':' . getMinute($hourEnd));

$dateInterval = ($dateEnd - $dateStart) / 300;
if ($dateInterval < 0) echo "Date Start cant be ahead of Date End";
else {
    $where = '1';
    $where .= " AND (`date` BETWEEN $yearStart$monthStart$dayStart AND $yearEnd$monthEnd$dayEnd)";
    if ($hourStart <= $hourEnd) $where .= " AND (`time` BETWEEN $hourStart AND $hourEnd)";
    if ($keyword) $where .= " AND keyword = '$keyword'";
    if ($platform) $where .= " AND platform = '$platform'";
//    if ($name) $where .= " AND `name` LIKE '$name%'";

    $q = "SELECT idx, platform, keyword, `date`, `time`, `name`, title, `type`, `rank` FROM keyword_ranks WHERE $where";
    print_r("$q<br>");


    $rows = $db->get_results($q);

    if (!empty($rows)) $graphs = prepareGraph($rows, $name);
}

?>

<h2>Keyword Rank Statistics</h2>
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
                    <select name="platform">
                        <option value="desktop" <?php if ($platform == 'desktop') echo ' selected' ?>>Desktop</option>
                        <option value="mobile" <?php if ($platform == 'mobile') echo ' selected' ?>>Mobile</option>
                    </select>
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

if (!empty($graphs)) {
    $dHeight = 200;
    $npHeight = 20;
    $npTop = $dHeight - $npHeight;

    foreach ($graphs as $title => $graph) {
        $header = "$title. " . implode(',', $graph['names']);
        echo "<h3>$header</h3>";

        if (!empty($graph['dates'])) {
            $y = 0;
            $m = 0;
            $d = 0;
            $h = 0;
            $min = 0;

            $daysInterval = $dayEnd - $dayStart;
            for ($i = 0; $i <= $daysInterval; $i++) {
                $dates = date("Ymd", mktime(0, 0, 0, $monthStart, $dayStart + $i, $yearStart));
                if (!array_key_exists($dates, $graph['dates'])) {
                    $graph['dates'][$dates] = ["0000"];
                }
            }

            ksort($graph['dates'], 1);

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


                foreach ($times as $time) {
                    if ($time == 'x') {
                        $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: black;'";
                        echo "<span $attr></span>";
                        continue;
                    }
                    global $h;
                    global $min;
                    $h = getHour($time);
                    $min = getMinute($time);
                    $currentDate = date("M d Y", mktime($h, $min, 0, $m, $d, $y));
                    $dateEnd = strtotime("$y-$m-$d " . $h . ':' . $min);
                    $dateInterval = floor((($dateEnd - $dateStart) / 300));

                    /**
                     * Red lines from start and in between
                     */
                    for ($i = 1; $i < $dateInterval; $i++) {
//                        print_r(floor((($min + $i - 1) * 5) / 60));
//                        print_r("~~~add0($h + floor((($min + $i - 1) * 5) / 60)) . add0((($min + $i - 1) * 5) % 60)~~~");
                        $currentTime = add0($h + floor(($min + ($i - 1) * 5) / 60)) . add0(($min + ($i - 1) * 5) % 60);
                        echo "@@@(int)$currentTime < (int)$hourStart || (int)$currentTime > (int)$hourEnd@@@";
                        if ((int)$currentTime > (int)$hourEnd) break;
                        if ((int)$currentTime < (int)$hourStart) continue;
                        $now = date("M d Y h:ia", mktime($h, $i * 5, 0, $monthStart, $dayStart, $yearStart));
                        $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[5];' title='$now'";
                        echo "<span $attr></span>";
                    }

                    if ($time < $hourStart) continue;

                    $rank = $graph['rank'];
                    $info = 'Rank: ' . $rank
                        . ' Keyword: ' . $graph['keyword'] . ', '
                        . ' Name: ' . $name . ', '
                        . $currentDate . ' ' . showTime($time);
                    $height = (6 - $rank) * ($dHeight / 5);
                    $top = $dHeight - $height;
                    $color = $colors[(int)$rank - 1];

                    $data = "style='height:$height" . "px; margin-top:$top" . "px; background-color: $color;' title='$info'";
                    echo "<span $data></span>";
                    $dateStart = $dateEnd;
                }

                $dateEnd = strtotime("$yearEnd-$monthEnd-$dayEnd " . getHour($hourEnd) . ':' . getMinute($hourEnd));
                $dateInterval = ($dateEnd - $dateStart) / 300;
                /**
                 * Red lines after the record until the end of selected date
                 */
                for ($i = 1; $i < $dateInterval; $i++) {
                    $currentTime = add0($h + floor(($min + $i  * 5) / 60)) . add0(($min + $i * 5) % 60);
                    echo "###$currentTime > $hourStart && $currentTime > $hourEnd###";
                    if((int)$currentTime > (int)$hourEnd) break;
                    if ((int)$currentTime < (int)$hourStart) continue;
                    $now = date("M d Y h:ia", mktime($h, ($i * 5) + $min, 0, $m, $d, $y));
                    $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[5];' title='$now'";
                    echo "<span $attr></span>";
                }


                echo "</div>";
            }
            echo "</div>";
            echo "</div>";

        } else {
            /**
             * red lines if no record is found
             */
            $dateStart = strtotime("$yearStart-$monthStart-$dayStart " . getHour($hourStart) . ':' . getMinute($hourStart));
            $dateEnd = strtotime("$yearEnd-$monthEnd-$dayEnd " . getHour($hourEnd) . ':' . getMinute($hourEnd));
            $dateInterval = ($dateEnd - $dateStart) / 300;

            for ($i = 1; $i < $dateInterval; $i++) {

                $now = date("M d Y h:ia", mktime(0, $i * 5, 0, $monthStart, $dayStart, $yearStart));
                $attr = "style='height:$npHeight" . "px; margin-top:$npTop" . "px; background-color: $colors[5];' title='$now'";
                echo "<span $attr></span>";
            }
        }

    }
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

