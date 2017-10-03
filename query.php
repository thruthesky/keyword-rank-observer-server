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


//$data = $_REQUEST;
//print_r($data);

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
$hourStart = '0000';
$hourEnd = '2300';
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


$where = '1';
$where .= " AND `date` BETWEEN $yearStart$monthStart$dayStart AND $yearEnd$monthEnd$dayEnd";
if ($hourStart <= $hourEnd) $where .= " AND `time` BETWEEN $hourStart AND $hourEnd";
if ($keyword) $where .= " AND keyword LIKE '%$keyword%'";
if ($platform) $where .= " AND platform = '$platform'";
if ($name) $where .= " AND `name` LIKE '%$name%'";

//print_r('<br>' . 'WHERE ' . $where . '<br>');
$rows = $db->get_results("SELECT * FROM keyword_ranks WHERE $where");

?>

<h2>Key work rank statistics</h2>
<form method="GET">
    <nav class="searchOption">
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
                        for ($i = 0; $i < 24; $i++) {
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

<?php
if (!empty($rows)) {

    echo "<div class=\"statisticGraph\">";
    echo "<div class=\"bar\">";
    foreach ($rows as $row) {

        $info = 'Date: ' . $row->date . ' ' . showTime($row->time);
        $height = (6 - $row->rank) * 20;
        $top = 100 - $height;

        $data = "style='height:$height" . "px; top:$top" . "px;' title='$info'";
        echo "<span $data></span>";

    }
}
echo "</div>";
echo "</div>";
?>

</body>
</html>

